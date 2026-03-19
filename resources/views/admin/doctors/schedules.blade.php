<x-admin-layout title="Horarios | Simify"
                :breadcrumbs="[
    ['name' => 'Dashboard',
    'href' => route('admin.dashboard')],

    ['name' => 'Doctores',
    'href' => route('admin.doctors.index'),],
    ['name' => 'Horarios',],
]">

    <x-wire-card>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gestor de horarios</h2>
                <p class="text-sm text-gray-500">{{ $doctor->user->name }} - {{ $doctor->speciality?->name ?? 'Sin especialidad' }}</p>
            </div>
            <x-wire-button>
                Guardar horario
            </x-wire-button>
        </div>

        @php
            $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
            $hours = [];
            for ($h = 8; $h < 18; $h++) {
                $hours[] = sprintf('%02d:00:00', $h);
            }
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b">
                        <th class="px-4 py-3 font-semibold text-gray-700 uppercase">Día/Hora</th>
                        @foreach ($days as $day)
                            <th class="px-4 py-3 font-semibold text-gray-700 uppercase text-center">{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hours as $hour)
                        <tr class="border-b">
                            <td class="px-4 py-3 font-medium text-gray-900 align-top">
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 mr-2">
                                    {{ $hour }}
                                </div>
                            </td>
                            @foreach ($days as $dayIndex => $day)
                                <td class="px-4 py-2 text-center">
                                    <div class="space-y-1">
                                        <label class="flex items-center text-xs text-gray-500">
                                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 mr-1">
                                            Todos
                                        </label>
                                        @for ($q = 0; $q < 4; $q++)
                                            @php
                                                $startMin = $q * 15;
                                                $endMin = ($q + 1) * 15;
                                                $hNum = intval(substr($hour, 0, 2));
                                                $slotStart = sprintf('%02d:%02d', $hNum, $startMin);
                                                $slotEnd = $endMin == 60 ? sprintf('%02d:00', $hNum + 1) : sprintf('%02d:%02d', $hNum, $endMin);
                                            @endphp
                                            <label class="flex items-center text-xs text-gray-600">
                                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 mr-1"
                                                       name="slots[]" value="{{ $day }}_{{ $slotStart }}">
                                                {{ $slotStart }} - {{ $slotEnd }}
                                            </label>
                                        @endfor
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-wire-card>

</x-admin-layout>
