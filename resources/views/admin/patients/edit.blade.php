{{-- Logica de PHP para manejar errores y controlar la pestaña activa--}}
@php
    // Definir qué campos pertenecen a cada pestaña
    $errorGroups = [
        'antecedentes' => ['allergies', 'chronic_conditions', 'surgical_history', 'family_history'],
        'informacion-general' => ['blood_type_id', 'observations'],
        'contacto-emergencia' => ['emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship'],
    ];

    // Pestaña por defecto
    $initialTab = 'datos-personales';

    // Si hay errores buscamos en qué pestaña pasó
    foreach ($errorGroups as $tabName => $fields) {
        if ($errors->hasAny($fields)) {
            $initialTab = $tabName;
            break;
        }
    }
@endphp


<x-admin-layout title="Pacientes | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard',
    'href' => route('admin.dashboard')],

    ['name' => 'Pacientes',
    'href' => route('admin.patients.index'),],
    ['name' => 'Editar',],
]">

    <form action="{{ route('admin.patients.update', $patient) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        {{-- Header con accion --}}
        <x-wire-card class="mb-8">
            <div class="flex justify-between items-center"> <!-- Justificacion por enmedio -->
                <div class="flex items-center">
                    <img src="{{ $patient->profile_photo_url }}" alt="{{ $patient->name }}" class="h-20 w-20 rounded-full object-cover object-center mr-4">
                    <div><p class="text-2xl font-bold text-gray-900 ml-4">{{ $patient->user->name }}</p></div>

                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.patients.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit" >
                        <i class="fa-solid fa-check"></i>
                        Guardar cambios
                    </x-wire-button>

                </div>
            </div>
        </x-wire-card>

        {{--Tabs de navegacion--}}
        <x-wire-card>

            {{--Menú de navegación--}}
            <x-tabs :active="$initialTab">

                <x-slot name="header"> <!--Aqui esta el header que encapsula a los tabs-->
                    <x-tabs-link tab="datos-personales">
                        <i class="fa-solid fa-user me-2"></i>
                        Datos personales
                    </x-tabs-link>

                    <x-tabs-link
                        tab="antecedentes"
                        :error="$errors->hasAny($errorGroups['antecedentes'])">
                        <i class="fa-solid fa-file-medical me-2"></i>
                        Antecedentes
                    </x-tabs-link>

                    <x-tabs-link
                        tab="informacion-general"
                        :error="$errors->hasAny($errorGroups['informacion-general'])">
                        <i class="fa-solid fa-info me-2"></i>
                        Información general
                    </x-tabs-link>

                    <x-tabs-link
                        tab="contacto-emergencia"
                        :error="$errors->hasAny($errorGroups['contacto-emergencia'])">
                        <i class="fa-solid fa-heart me-2"></i>
                        Contacto de emergencia
                    </x-tabs-link>
                </x-slot>



                {{--Contenido Tab1 : Datos personales--}}
                <x-tab-content tab="datos-personales">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            {{--lado izquierdo--}}
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fa-solid fa-user-gear text-blue-500 text-xl mt-1"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-blue-800">Edición de cuenta de usuario</h3>
                                    <div class="mt-1 text-sm text-blue-700">
                                        <p>
                                            La información de acceso (nombre, email y contraseña) se gestiona desde la sección de <strong>Usuarios</strong> asociada
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{--Lado derecho : Boton de acción--}}
                            <div class="flec-shirk-0">
                                <x-wire-button primary sm href="{{ route('admin.users.edit', $patient->user) }}" target="_blank">
                                    <i class="fa-solid fa-arrow-up-right-from-square ms-2"></i>
                                    Editar usuario
                                </x-wire-button>
                            </div>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-500 font-semibold">Telefono:</span>
                            <span class="text-gray-900 text-sm ml-1">{{ $patient->user->phone }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-semibold">Email:</span>
                            <span class="text-gray-900 text-sm ml-1">{{ $patient->user->email }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-semibold">Dirección:</span>
                            <span class="text-gray-900 text-sm ml-1">{{ $patient->user->adress }}</span>
                        </div>
                    </div>
                </x-tab-content>

                {{--Contenido Tab2 : Antecedentes medicos--}}
                <x-tab-content tab="antecedentes">
                    <div class="grid lg:grid-cols-2 gap-4">
                        <div>
                            <x-wire-textarea label="Alergias conocidas" name="allergies" >
                                {{ old('allergies', $patient->allergies) }}
                            </x-wire-textarea>
                        </div>
                        <div>
                            <x-wire-textarea label="Enfermedades cronicas" name="chronic_conditions" >
                                {{ old('chronic_conditions', $patient->chronic_conditions) }}
                            </x-wire-textarea>
                        </div>
                        <div>
                            <x-wire-textarea label="Antecedentes quirurgicos" name="surgical_history" >
                                {{ old('surgical_history', $patient->surgical_history) }}
                            </x-wire-textarea>
                        </div>
                        <div>
                            <x-wire-textarea label="Historial familiar" name="family_history" >
                                {{ old('family_history', $patient->family_history) }}
                            </x-wire-textarea>
                        </div>
                    </div>
                </x-tab-content>

                {{--Contenido Tab3 : Información general--}}
                <x-tab-content tab="informacion-general">
                    <div>
                        <x-wire-native-select label="Tipo de sangre" class="mb-4" name="blood_type_id">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="" >Selecciona un tipo de sangre</option>
                            @foreach($bloodTypes as $bloodType)
                                <option value="{{ $bloodType->id }}" @selected(old('blood_type_id', $patient->blood_type_id) == $bloodType->id)>
                                    {{ $bloodType->name }}
                                </option>
                            @endforeach
                        </x-wire-native-select>

                        <x-wire-textarea label="Observaciones" name="observations" >
                            {{ old('observations', $patient->observations) }}
                        </x-wire-textarea>

                    </div>
                </x-tab-content>

                {{--Contenido Tab4 : Contacto de emergencia--}}
                <x-tab-content tab="contacto-emergencia">
                    <div class="space-y-4">
                        <x-wire-input label="Nombre del contacto" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" />
                        <x-wire-phone label="Teléfono del contacto" name="emergency_contact_phone" mask="(###) ###-####" placeholder="(999) 999-9999" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" />
                        <x-wire-input label="Relación con el paciente" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $patient->emergency_contact_relationship) }}" />
                    </div>
                </x-tab-content>
            </x-tabs>
        </x-wire-card>


    </form>


</x-admin-layout>
