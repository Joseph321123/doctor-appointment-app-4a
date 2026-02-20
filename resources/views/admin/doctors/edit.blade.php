<x-admin-layout title="Doctores | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard',
    'href' => route('admin.dashboard')],

    ['name' => 'Doctores',
    'href' => route('admin.doctors.index'),],
    ['name' => 'Editar',],
]">

    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        {{-- Header con accion --}}
        <x-wire-card class="mb-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ $doctor->user->profile_photo_url }}" alt="{{ $doctor->user->name }}" class="h-20 w-20 rounded-full object-cover object-center mr-4">
                    <div>
                        <p class="text-2xl font-bold text-gray-900 ml-4">{{ $doctor->user->name }}</p>
                        <p class="text-sm text-gray-500 ml-4">Especialidad: {{ $doctor->speciality?->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500 ml-4">Licencia: {{ $doctor->medical_license_number ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit" >
                        <i class="fa-solid fa-check"></i>
                        Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- Formulario sin pestañas --}}
        <x-wire-card>
            <div class="space-y-4">
                <x-wire-native-select label="Especialidad" name="speciality_id">
                    <option value="">Seleccione una especialidad</option>
                    @foreach($specialities as $speciality)
                        <option value="{{ $speciality->id }}" @selected(old('speciality_id', $doctor->speciality_id) == $speciality->id)>
                            {{ $speciality->name }}
                        </option>
                    @endforeach
                </x-wire-native-select>

                <x-wire-input label="Número de licencia médica" name="medical_license_number"
                              value="{{ old('medical_license_number', $doctor->medical_license_number) }}" />

                <x-wire-textarea label="Biografía" name="biography">{{ old('biography', $doctor->biography) }}</x-wire-textarea>
            </div>
        </x-wire-card>
    </form>

</x-admin-layout>
