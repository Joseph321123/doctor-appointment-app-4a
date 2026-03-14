<x-admin-layout title="Citas médicas | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard',
    'href' => route('admin.dashboard')],

    ['name' => 'Citas',
    'href' => route('admin.appointments.index'),],
    ['name' => 'Editar',],
]">

    <x-wire-card>
        <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="grid lg:grid-cols-2 gap-4">
                    <x-wire-native-select name="patient_id" label="Paciente" required>
                        <option value="">Seleccione un paciente</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                                {{ $patient->user->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>

                    <x-wire-native-select name="doctor_id" label="Doctor" required>
                        <option value="">Seleccione un doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(old('doctor_id', $appointment->doctor_id) == $doctor->id)>
                                {{ $doctor->user->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                <div class="grid lg:grid-cols-3 gap-4">
                    <x-wire-input type="date" name="date" label="Fecha" required
                                  min="{{ date('Y-m-d') }}"
                                  value="{{ old('date', $appointment->date->format('Y-m-d')) }}" />
                    <x-wire-input type="time" name="start_time" label="Hora de inicio" required
                                  value="{{ old('start_time', $appointment->start_time) }}" />
                    <x-wire-input type="time" name="end_time" label="Hora de fin" required
                                  value="{{ old('end_time', $appointment->end_time) }}" />
                </div>

                <x-wire-native-select name="status" label="Estado" required>
                    <option value="1" @selected(old('status', $appointment->status) == 1)>Programado</option>
                    <option value="2" @selected(old('status', $appointment->status) == 2)>Completado</option>
                    <option value="3" @selected(old('status', $appointment->status) == 3)>Cancelado</option>
                </x-wire-native-select>

                <x-wire-textarea name="reason" label="Motivo de la cita">{{ old('reason', $appointment->reason) }}</x-wire-textarea>

                <div class="flex justify-end space-x-3">
                    <x-wire-button outline gray href="{{ route('admin.appointments.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check me-2"></i>
                        Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </form>
    </x-wire-card>

</x-admin-layout>
