<?php

namespace App\Services;

use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Throwable;

/**
 * Importa filas de Excel/CSV a User + Patient (rol Paciente).
 * Pensado para ejecutarse en la misma petición HTTP (sin depender del worker de cola).
 */
class ImportPacientesService
{
    /**
     * @return array{imported: int, skipped: int, ok: bool, error: ?string}
     */
    public function import(string $absoluteFilePath): array
    {
        if (! is_readable($absoluteFilePath)) {
            return [
                'imported' => 0,
                'skipped' => 0,
                'ok' => false,
                'error' => 'No se pudo leer el archivo subido.',
            ];
        }

        $zipHint = $this->zipExtensionMissingMessage($absoluteFilePath);
        if ($zipHint !== null) {
            return [
                'imported' => 0,
                'skipped' => 0,
                'ok' => false,
                'error' => $zipHint,
            ];
        }

        $imported = 0;
        $skipped = 0;

        try {
            $spreadsheet = $this->loadSpreadsheet($absoluteFilePath);
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

            foreach (array_slice($rows, 1) as $index => $row) {
                $lineNumber = $index + 2;

                $cells = array_values(is_array($row) ? $row : []);

                $nombreCompleto = $this->stringCell($cells[0] ?? null);
                if ($nombreCompleto === '') {
                    $skipped++;

                    continue;
                }

                $correo = $this->stringCell($cells[1] ?? null);
                $telefono = $this->stringCell($cells[2] ?? null);
                $fechaNacimientoRaw = $cells[3] ?? null;
                $tipoSangreRaw = $this->stringCell($cells[4] ?? null);
                $alergias = $this->stringCell($cells[5] ?? null);

                $email = $this->resolveEmail($correo, $lineNumber);

                if (User::query()->where('email', $email)->exists()) {
                    Log::error('Importación pacientes: correo duplicado, fila omitida.', [
                        'line' => $lineNumber,
                        'email' => $email,
                    ]);
                    $skipped++;

                    continue;
                }

                $birthDate = $this->parseBirthDate($fechaNacimientoRaw);

                try {
                    DB::transaction(function () use (
                        $nombreCompleto,
                        $email,
                        $telefono,
                        $tipoSangreRaw,
                        $alergias,
                        $birthDate,
                        $lineNumber,
                        &$imported
                    ) {
                        $user = User::create([
                            'name' => $nombreCompleto,
                            'email' => $email,
                            'password' => Hash::make(Str::password(20)),
                            'id_number' => 'IMP-'.strtoupper(Str::random(12)),
                            'phone' => $telefono !== '' ? $telefono : '0000000000',
                            'address' => 'Importación masiva',
                        ]);

                        if (! $user->hasRole('Paciente')) {
                            $user->assignRole('Paciente');
                        }

                        $bloodTypeId = $this->resolveBloodTypeId($tipoSangreRaw, $lineNumber);

                        Patient::create([
                            'user_id' => $user->id,
                            'blood_type_id' => $bloodTypeId,
                            'birth_date' => $birthDate,
                            'allergies' => $alergias !== '' ? $alergias : null,
                        ]);

                        $imported++;
                    });
                } catch (Throwable $e) {
                    Log::error('Importación pacientes: error al insertar fila.', [
                        'line' => $lineNumber,
                        'message' => $e->getMessage(),
                        'exception' => $e,
                    ]);
                }
            }

            Log::info('Importación de pacientes finalizada.', [
                'file' => $absoluteFilePath,
                'imported' => $imported,
                'skipped' => $skipped,
            ]);

            return [
                'imported' => $imported,
                'skipped' => $skipped,
                'ok' => true,
                'error' => null,
            ];
        } catch (Throwable $e) {
            Log::error('Importación pacientes: fallo al leer o procesar el archivo.', [
                'path' => $absoluteFilePath,
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return [
                'imported' => $imported,
                'skipped' => $skipped,
                'ok' => false,
                'error' => $this->humanizeReaderError($e->getMessage(), $absoluteFilePath),
            ];
        } finally {
            if (is_file($absoluteFilePath)) {
                @unlink($absoluteFilePath);
            }
        }
    }

    /**
     * Los .xlsx son archivos ZIP; PhpSpreadsheet necesita la extensión PHP "zip" (clase ZipArchive).
     */
    private function zipExtensionMissingMessage(string $path): ?string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (! in_array($ext, ['xlsx', 'xlsm', 'xltx', 'xltm'], true)) {
            return null;
        }

        if (extension_loaded('zip') && class_exists(\ZipArchive::class)) {
            return null;
        }

        return 'Para leer archivos .xlsx hace falta la extensión PHP zip (ZipArchive). '
            .'En Windows/XAMPP: edita php.ini, descomenta `extension=zip` o `extension=php_zip.dll`, guarda y reinicia Apache (o el servidor que uses). '
            .'Mientras tanto puedes exportar la hoja a CSV en Excel e importar el archivo .csv (no requiere zip).';
    }

    private function humanizeReaderError(string $message, string $path): string
    {
        if (str_contains($message, 'ZipArchive')) {
            return $this->zipExtensionMissingMessage($path)
                ?? 'Falta la extensión PHP zip para abrir este Excel. Exporta a CSV o habilita extension=zip en php.ini.';
        }

        return $message;
    }

    /**
     * CSV con coma o punto y coma; Excel con IOFactory estándar.
     */
    private function loadSpreadsheet(string $path): Spreadsheet
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, ['csv', 'txt'], true)) {
            $reader = new CsvReader;
            $reader->setInputEncoding('UTF-8');
            $reader->setDelimiter($this->detectCsvDelimiter($path));
            $reader->setEnclosure('"');
            $reader->setEscapeCharacter('\\');

            return $reader->load($path);
        }

        return IOFactory::load($path);
    }

    private function detectCsvDelimiter(string $path): string
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return ',';
        }
        $firstLine = fgets($handle) ?: '';
        fclose($handle);

        $commas = substr_count($firstLine, ',');
        $semis = substr_count($firstLine, ';');

        return $semis > $commas ? ';' : ',';
    }

    private function stringCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_float($value) || is_int($value)) {
            return trim((string) $value);
        }

        return trim((string) $value);
    }

    private function resolveEmail(string $correo, int $lineNumber): string
    {
        if ($correo !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $correo;
        }

        return 'import.'.$lineNumber.'.'.uniqid('', true).'@import.local';
    }

    private function resolveBloodTypeId(string $raw, int $lineNumber): ?int
    {
        $normalized = strtoupper(str_replace([' ', 'RH'], '', trim($raw)));
        if ($normalized === '') {
            return null;
        }

        if (! preg_match('/^(A|B|AB|O)[+-]$/', $normalized)) {
            Log::error('Importación pacientes: tipo de sangre no reconocido; se guarda null.', [
                'line' => $lineNumber,
                'raw' => $raw,
            ]);

            return null;
        }

        $bloodType = BloodType::query()->firstOrCreate(
            ['name' => $normalized],
            ['name' => $normalized]
        );

        return $bloodType->id;
    }

    private function parseBirthDate(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
            } catch (Throwable) {
                //
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (Throwable) {
            Log::error('Importación pacientes: fecha de nacimiento no reconocida.', [
                'value' => $value,
            ]);

            return null;
        }
    }
}
