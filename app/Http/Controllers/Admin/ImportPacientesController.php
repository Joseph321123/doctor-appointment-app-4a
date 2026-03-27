<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImportPacientesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ImportPacientesController extends Controller
{
    public function index(): View
    {
        return view('admin.import-pacientes.index');
    }

    public function store(Request $request, ImportPacientesService $importPacientes): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:xlsx,xls,csv,txt'],
        ], [
            'file.required' => 'Selecciona un archivo Excel o CSV.',
            'file.mimes' => 'Formatos permitidos: .xlsx, .xls, .csv.',
        ]);

        @ini_set('max_execution_time', '300');

        // El disco "local" apunta a storage/app/private (ver config/filesystems.php).
        $storedPath = $validated['file']->store('imports', 'local');

        if ($storedPath === false) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No se pudo guardar el archivo en el servidor.',
                    'errors' => ['file' => ['No se pudo guardar el archivo en el servidor.']],
                ], 422);
            }

            return redirect()
                ->route('admin.import-pacientes.index')
                ->withErrors(['file' => 'No se pudo guardar el archivo en el servidor.']);
        }

        $absolutePath = Storage::disk('local')->path($storedPath);

        $result = $importPacientes->import($absolutePath);

        if (! $result['ok']) {
            $msg = $result['error'] ?? 'No se pudo completar la importación.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $msg,
                    'errors' => ['file' => [$msg]],
                ], 422);
            }

            return redirect()
                ->route('admin.import-pacientes.index')
                ->withErrors(['file' => $msg]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'result' => $result,
            ]);
        }

        return redirect()
            ->route('admin.import-pacientes.index')
            ->with('import_result', $result);
    }
}
