<x-admin-layout title="Citas médicas | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard',
    'href' => route('admin.dashboard')],

    ['name' => 'Citas',],
]">
    <div class="mb-4 flex justify-end">
        <x-wire-button href="{{ route('admin.appointments.create') }}">
            <i class="fa-solid fa-plus me-2"></i>
            Nuevo
        </x-wire-button>
    </div>

    @livewire('admin.datatables.appointment-table')
</x-admin-layout>
