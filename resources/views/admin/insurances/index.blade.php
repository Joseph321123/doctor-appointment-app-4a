<x-admin-layout title="Aseguradoras | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard',
    'href' => route('admin.dashboard')],

    ['name' => 'Aseguradoras',],
]">

    {{-- Slot de acción: botón Nueva Aseguradora --}}
    <x-slot name="action">
        <a href="{{ route('admin.insurances.create') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
            <i class="fa-solid fa-plus me-2"></i>
            Nueva Aseguradora
        </a>
    </x-slot>

    {{-- Tabla de aseguradoras --}}
    <div class="max-w-7xl mx-auto">
        <x-wire-card>
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Nombre de la empresa</th>
                            <th scope="col" class="px-6 py-3">Teléfono de contacto</th>
                            <th scope="col" class="px-6 py-3">Fecha de registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($insurances as $insurance)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $insurance->id }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $insurance->nombre_empresa }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $insurance->telefono_contacto }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $insurance->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-shield-halved text-3xl mb-2"></i>
                                    <p class="mt-2">No hay aseguradoras registradas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-wire-card>
    </div>

</x-admin-layout>
