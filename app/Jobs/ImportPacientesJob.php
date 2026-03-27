<?php

namespace App\Jobs;

use App\Services\ImportPacientesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Versión en cola del importador (opcional). El flujo normal usa ImportPacientesService en el controlador.
 */
class ImportPacientesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public function __construct(
        public readonly string $absoluteFilePath
    ) {}

    public function handle(ImportPacientesService $service): void
    {
        $result = $service->import($this->absoluteFilePath);

        if (! $result['ok']) {
            Log::error('ImportPacientesJob: importación no completada.', ['result' => $result]);

            throw new \RuntimeException($result['error'] ?? 'Error desconocido en importación.');
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('ImportPacientesJob: trabajo de cola fallido.', [
            'path' => $this->absoluteFilePath,
            'message' => $exception?->getMessage(),
            'exception' => $exception,
        ]);
    }
}
