<x-admin-layout title="Importación | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Importación de pacientes'],
]">

    @if ($errors->has('file'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
            {{ $errors->first('file') }}
        </div>
    @endif

    <div id="import-result-slot" class="mb-4">
        @session('import_result')
            @php
                /** @var array{imported: int, skipped: int, ok: bool, error: ?string} $r */
                $r = $value;
                $isWarning = ($r['imported'] ?? 0) === 0;
            @endphp
            <div @class([
                'rounded-lg border p-4',
                'border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100' => $isWarning,
                'border-green-200 bg-green-50 text-green-900 dark:border-green-800 dark:bg-green-900/30 dark:text-green-100' => ! $isWarning,
            ])>
                <p class="font-medium">Importación terminada</p>
                <p class="mt-1 text-sm">
                    Se registraron <strong>{{ $r['imported'] }}</strong> paciente(s) como usuarios con rol Paciente.
                    @if (($r['skipped'] ?? 0) > 0)
                        <span @class([
                            'block mt-1',
                            'text-amber-900/90 dark:text-amber-200/90' => $isWarning,
                            'text-green-800 dark:text-green-200' => ! $isWarning,
                        ])>{{ $r['skipped'] }} fila(s) omitida(s) (vacías o correo ya existente).</span>
                    @endif
                </p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <x-wire-button blue href="{{ route('admin.users.index') }}">
                        <i class="fa-solid fa-users me-2"></i>
                        Ver usuarios
                    </x-wire-button>
                    <x-wire-button outline gray href="{{ route('admin.patients.index') }}">
                        <i class="fa-solid fa-user-injured me-2"></i>
                        Ver pacientes
                    </x-wire-button>
                </div>
            </div>
        @endsession
    </div>

    {{-- Barra compacta abajo a la derecha: no tapa toda la pantalla; puedes seguir usando el menú. --}}
    <div
        id="import-loading"
        class="pointer-events-none fixed bottom-4 right-4 z-50 hidden max-w-sm rounded-xl border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-600 dark:bg-gray-800"
        role="status"
        aria-live="polite"
        aria-label="Importando pacientes"
    >
        <p class="text-sm font-semibold text-gray-900 dark:text-white">Importando en segundo plano…</p>
        <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-400">Puedes usar el menú lateral con normalidad. No cierres esta pestaña; si entras a otra pantalla <strong>en esta misma pestaña</strong>, el navegador puede cancelar la importación.</p>
        <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
            <div class="import-progress-bar h-full w-1/3 rounded-full bg-indigo-600"></div>
        </div>
    </div>

    <style>
        @keyframes import-progress-slide {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(400%); }
        }
        .import-progress-bar {
            animation: import-progress-slide 1.1s ease-in-out infinite;
        }
    </style>

    <div class="max-w-2xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Sube un archivo <strong>.xlsx</strong>, <strong>.xls</strong> o <strong>.csv</strong> con una fila de encabezado y, en este orden, las columnas:
            <code class="rounded bg-gray-100 px-1 text-xs dark:bg-gray-700">nombre_completo</code>,
            <code class="rounded bg-gray-100 px-1 text-xs dark:bg-gray-700">correo</code>,
            <code class="rounded bg-gray-100 px-1 text-xs dark:bg-gray-700">telefono</code>,
            <code class="rounded bg-gray-100 px-1 text-xs dark:bg-gray-700">fecha_nacimiento</code>,
            <code class="rounded bg-gray-100 px-1 text-xs dark:bg-gray-700">tipo_sangre</code>,
            <code class="rounded bg-gray-100 px-1 text-xs dark:bg-gray-700">alergias</code>.
        </p>

        <form id="form-import-pacientes" action="{{ route('admin.import-pacientes.store') }}" method="post" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="file" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Archivo</label>
                <input type="file" name="file" id="file" required accept=".xlsx,.xls,.csv,.txt"
                       class="block w-full text-sm text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                <p id="import-file-error" class="mt-1 hidden text-sm text-red-600"></p>
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end">
                <x-wire-button type="submit" id="btn-import-submit">
                    <i class="fa-solid fa-file-import me-2"></i>
                    Importar ahora
                </x-wire-button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            var form = document.getElementById('form-import-pacientes');
            var loading = document.getElementById('import-loading');
            var slot = document.getElementById('import-result-slot');
            var errEl = document.getElementById('import-file-error');
            var btn = document.getElementById('btn-import-submit');
            var csrf = document.querySelector('meta[name="csrf-token"]');
            if (!form || !loading || !csrf) return;

            var routes = {
                users: @json(route('admin.users.index')),
                patients: @json(route('admin.patients.index')),
            };

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var fileInput = document.getElementById('file');
                if (!fileInput || !fileInput.files.length) return;

                errEl.classList.add('hidden');
                errEl.textContent = '';
                if (slot) slot.innerHTML = '';

                loading.classList.remove('hidden');
                if (btn) btn.setAttribute('disabled', 'disabled');

                var fd = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf.getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: fd,
                    credentials: 'same-origin',
                })
                    .then(function (res) {
                        return res.json().then(function (data) {
                            return { ok: res.ok, status: res.status, data: data };
                        });
                    })
                    .then(function (payload) {
                        loading.classList.add('hidden');
                        if (btn) btn.removeAttribute('disabled');

                        if (!payload.ok) {
                            var msg = (payload.data && payload.data.errors && payload.data.errors.file && payload.data.errors.file[0])
                                || (payload.data && payload.data.message)
                                || 'No se pudo completar la importación.';
                            errEl.textContent = msg;
                            errEl.classList.remove('hidden');
                            return;
                        }

                        var r = payload.data.result;
                        if (!r || !slot) return;

                        var warning = (r.imported || 0) === 0;
                        var boxClass = warning
                            ? 'rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-950 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100'
                            : 'rounded-lg border border-green-200 bg-green-50 p-4 text-green-900 dark:border-green-800 dark:bg-green-900/30 dark:text-green-100';
                        var skipClass = warning
                            ? 'block mt-1 text-amber-900/90 dark:text-amber-200/90'
                            : 'block mt-1 text-green-800 dark:text-green-200';

                        var skippedHtml = (r.skipped || 0) > 0
                            ? '<span class="' + skipClass + '">' + r.skipped + ' fila(s) omitida(s) (vacías o correo ya existente).</span>'
                            : '';

                        slot.innerHTML =
                            '<div class="' + boxClass + '">' +
                            '<p class="font-medium">Importación terminada</p>' +
                            '<p class="mt-1 text-sm">Se registraron <strong>' + (r.imported || 0) + '</strong> paciente(s) como usuarios con rol Paciente.' +
                            skippedHtml +
                            '</p>' +
                            '<div class="mt-4 flex flex-wrap gap-3">' +
                            '<a href="' + routes.users + '" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">' +
                            '<i class="fa-solid fa-users me-2"></i> Ver usuarios</a>' +
                            '<a href="' + routes.patients + '" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">' +
                            '<i class="fa-solid fa-user-injured me-2"></i> Ver pacientes</a>' +
                            '</div></div>';

                        slot.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    })
                    .catch(function () {
                        loading.classList.add('hidden');
                        if (btn) btn.removeAttribute('disabled');
                        errEl.textContent = 'Error de red o el servidor no respondió. Intenta de nuevo.';
                        errEl.classList.remove('hidden');
                    });
            });
        })();
    </script>
</x-admin-layout>
