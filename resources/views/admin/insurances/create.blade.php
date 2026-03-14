<x-admin-layout title="Nueva Aseguradora | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard',
    'href' => route('admin.dashboard')],

    ['name' => 'Aseguradoras',
    'href' => route('admin.insurances.index'),],
    ['name' => 'Nueva Aseguradora',],
]">

    <div class="max-w-3xl mx-auto">
        <x-wire-card>
            {{-- Encabezado del formulario --}}
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    Registrar Aseguradora
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Complete la información de la aseguradora o convenio para agregarla al directorio.
                </p>
            </div>

            <form action="{{ route('admin.insurances.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Nombre de la empresa --}}
                <div>
                    <x-wire-input
                        label="Nombre de la empresa"
                        name="nombre_empresa"
                        placeholder="Ej: Seguros Monterrey"
                        value="{{ old('nombre_empresa') }}"
                    />
                </div>

                {{-- Teléfono de contacto --}}
                <div>
                    <x-wire-input
                        label="Teléfono de contacto"
                        name="telefono_contacto"
                        placeholder="Ej: (999) 123-4567"
                        value="{{ old('telefono_contacto') }}"
                    />
                </div>

                {{-- Notas adicionales --}}
                <div>
                    <x-wire-textarea
                        label="Descripción detallada / Notas adicionales"
                        name="notas_adicionales"
                        rows="5"
                        placeholder="Ingrese información adicional sobre el convenio, cobertura, etc."
                    >{{ old('notas_adicionales') }}</x-wire-textarea>
                </div>

                {{-- Botones de acción --}}
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-wire-button
                        outline
                        gray
                        href="{{ route('admin.insurances.index') }}"
                    >
                        Cancelar
                    </x-wire-button>

                    <x-wire-button type="submit">
                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        Guardar Aseguradora
                    </x-wire-button>
                </div>
            </form>
        </x-wire-card>
    </div>

</x-admin-layout>
