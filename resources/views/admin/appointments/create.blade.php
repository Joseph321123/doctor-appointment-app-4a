<x-admin-layout title="Citas médicas | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard',
    'href' => route('admin.dashboard')],

    ['name' => 'Citas',
    'href' => route('admin.appointments.index'),],
    ['name' => 'Nuevo',],
]">

    <div x-data="{
        date: '{{ old('date') }}',
        timeRange: '',
        specialityFilter: '',
        selectedDoctor: null,
        selectedDoctorName: '',
        selectedDoctorSpeciality: '',
        selectedDoctorInitials: '',
        selectedSlot: '',
        startTime: '{{ old('start_time') }}',
        endTime: '{{ old('end_time') }}',
        showDoctors: false,

        doctors: @js($doctors->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->user->name,
            'speciality' => $d->speciality?->name ?? 'Sin especialidad',
            'speciality_id' => $d->speciality_id,
            'initials' => collect(explode(' ', $d->user->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join(''),
        ])),

        timeRanges: [
            {label: '08:00:00 - 09:00:00', slots: ['08:00', '08:15', '08:30', '08:45']},
            {label: '09:00:00 - 10:00:00', slots: ['09:00', '09:15', '09:30', '09:45']},
            {label: '10:00:00 - 11:00:00', slots: ['10:00', '10:15', '10:30', '10:45']},
            {label: '11:00:00 - 12:00:00', slots: ['11:00', '11:15', '11:30', '11:45']},
            {label: '12:00:00 - 13:00:00', slots: ['12:00', '12:15', '12:30', '12:45']},
            {label: '13:00:00 - 14:00:00', slots: ['13:00', '13:15', '13:30', '13:45']},
            {label: '14:00:00 - 15:00:00', slots: ['14:00', '14:15', '14:30', '14:45']},
            {label: '15:00:00 - 16:00:00', slots: ['15:00', '15:15', '15:30', '15:45']},
            {label: '16:00:00 - 17:00:00', slots: ['16:00', '16:15', '16:30', '16:45']},
            {label: '17:00:00 - 18:00:00', slots: ['17:00', '17:15', '17:30', '17:45']},
        ],

        get filteredDoctors() {
            let result = this.doctors;
            if (this.specialityFilter) {
                result = result.filter(d => d.speciality_id == this.specialityFilter);
            }
            return result;
        },

        get availableSlots() {
            if (!this.timeRange) return [];
            let range = this.timeRanges.find(r => r.label === this.timeRange);
            return range ? range.slots : [];
        },

        selectSlot(slot) {
            this.selectedSlot = slot;
            let h = parseInt(slot.split(':')[0]);
            let m = parseInt(slot.split(':')[1]);
            this.startTime = slot + ':00';
            m += 15;
            if (m >= 60) { h++; m = 0; }
            this.endTime = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':00';
        },

        selectDoctor(doctor) {
            this.selectedDoctor = doctor.id;
            this.selectedDoctorName = doctor.name;
            this.selectedDoctorSpeciality = doctor.speciality;
            this.selectedDoctorInitials = doctor.initials;
        },

        search() {
            this.showDoctors = true;
            this.selectedDoctor = null;
            this.selectedSlot = '';
            this.startTime = '';
            this.endTime = '';
        }
    }">

        {{-- Buscar disponibilidad --}}
        <x-wire-card class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-1">Buscar disponibilidad</h2>
            <p class="text-sm text-gray-500 mb-4">Encuentra el horario perfecto para tu cita.</p>

            <div class="grid lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date" x-model="date"
                           min="{{ date('Y-m-d') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                    <select x-model="timeRange"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccione un rango</option>
                        <template x-for="range in timeRanges" :key="range.label">
                            <option :value="range.label" x-text="range.label"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad (opcional)</label>
                    <select x-model="specialityFilter"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todas</option>
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="button" @click="search()"
                            :disabled="!date || !timeRange"
                            class="w-full px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Buscar disponibilidad
                    </button>
                </div>
            </div>
        </x-wire-card>

        {{-- Doctores y Resumen --}}
        <div x-show="showDoctors" x-cloak class="grid lg:grid-cols-3 gap-6">

            {{-- Lista de doctores (izquierda) --}}
            <div class="lg:col-span-2 space-y-4">
                <template x-for="doctor in filteredDoctors" :key="doctor.id">
                    <x-wire-card>
                        <div class="flex items-center mb-3">
                            <div class="h-12 w-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm mr-3">
                                <span x-text="doctor.initials"></span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900" x-text="doctor.name"></p>
                                <p class="text-sm text-blue-600" x-text="doctor.speciality"></p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-2">Horarios disponibles:</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="slot in availableSlots" :key="doctor.id + '-' + slot">
                                <button type="button"
                                        @click="selectDoctor(doctor); selectSlot(slot)"
                                        :class="selectedDoctor === doctor.id && selectedSlot === slot
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-blue-500 text-white hover:bg-blue-600'"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                                    <span x-text="slot + ':00'"></span>
                                </button>
                            </template>
                        </div>
                    </x-wire-card>
                </template>

                <div x-show="filteredDoctors.length === 0">
                    <x-wire-card>
                        <p class="text-center text-gray-500 py-4">No se encontraron doctores con los filtros seleccionados.</p>
                    </x-wire-card>
                </div>
            </div>

            {{-- Resumen de la cita (derecha) --}}
            <div>
                <x-wire-card>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Resumen de la cita</h3>

                    <div class="space-y-3 mb-6" x-show="selectedDoctor">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Doctor:</span>
                            <span class="text-gray-900 font-medium" x-text="selectedDoctorName"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Fecha:</span>
                            <span class="text-gray-900 font-medium" x-text="date"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Horario:</span>
                            <span class="text-gray-900 font-medium" x-text="startTime + ' – ' + endTime"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Duración:</span>
                            <span class="text-gray-900 font-medium">15 minutos</span>
                        </div>
                    </div>

                    <div x-show="!selectedDoctor" class="text-center text-gray-400 text-sm py-4 mb-4">
                        Seleccione un doctor y horario
                    </div>

                    <form action="{{ route('admin.appointments.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="doctor_id" :value="selectedDoctor">
                        <input type="hidden" name="date" :value="date">
                        <input type="hidden" name="start_time" :value="startTime">
                        <input type="hidden" name="end_time" :value="endTime">

                        @error('doctor_id')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                        @error('start_time')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                        @error('end_time')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror

                        <x-wire-native-select name="patient_id" label="Paciente" required>
                            <option value="">Seleccione un paciente</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                                    {{ $patient->user->name }}
                                </option>
                            @endforeach
                        </x-wire-native-select>
                        @error('patient_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <x-wire-textarea name="reason" label="Motivo de la cita">{{ old('reason') }}</x-wire-textarea>
                        @error('reason')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                                :disabled="!selectedDoctor || !date || !startTime"
                                class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Confirmar cita
                        </button>
                    </form>
                </x-wire-card>
            </div>
        </div>

    </div>

</x-admin-layout>
