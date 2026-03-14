<x-admin-layout title="Consulta | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard',
    'href' => route('admin.dashboard')],

    ['name' => 'Citas',
    'href' => route('admin.appointments.index'),],
    ['name' => 'Consulta',],
]">

    <x-wire-card>
        @livewire('admin.consultation-manager', ['appointment' => $appointment])
    </x-wire-card>

</x-admin-layout>
