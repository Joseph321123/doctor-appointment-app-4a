<div>
    @if (session()->has('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded-r-lg">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Header del paciente --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $appointment->patient->user->name }}</h2>
            <p class="text-sm text-gray-500">DNI: {{ $appointment->patient->user->id_number ?? 'N/A' }}</p>
        </div>
        <div class="flex space-x-3 mt-4 sm:mt-0">
            <x-wire-button outline gray href="{{ route('admin.patients.edit', $appointment->patient) }}">
                <i class="fa-solid fa-clock-rotate-left me-2"></i>
                Ver Historia
            </x-wire-button>
            <x-wire-button outline gray wire:click="$toggle('showPreviousConsultations')">
                <i class="fa-solid fa-folder-open me-2"></i>
                Consultas Anteriores
            </x-wire-button>
        </div>
    </div>

    {{-- Modal de Consultas Anteriores --}}
    <x-wire-modal wire:model="showPreviousConsultations" max-width="2xl">
        <x-wire-card title="Consultas Anteriores">
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse ($this->previousConsultations as $consultation)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">
                                    <i class="fa-solid fa-calendar me-1"></i>
                                    {{ $consultation->appointment?->date?->format('d/m/Y') ?? 'N/A' }} a las {{ $consultation->appointment?->start_time ?? '' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Atendido por: Dr(a). {{ $consultation->doctor?->user?->name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Diagnóstico:</strong> {{ $consultation->diagnosis ?? 'N/A' }}</p>
                            <p><strong>Tratamiento:</strong> {{ $consultation->treatment ?? 'N/A' }}</p>
                            <p><strong>Notas:</strong> {{ $consultation->notes ?? 'N/A' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No hay consultas anteriores para este paciente.</p>
                @endforelse
            </div>

            <x-slot name="footer">
                <x-wire-button flat wire:click="$set('showPreviousConsultations', false)">Cerrar</x-wire-button>
            </x-slot>
        </x-wire-card>
    </x-wire-modal>

    {{-- Pestañas Consulta / Receta --}}
    <div x-data="{ tab: 'consulta' }">
        <div class="border-b border-gray-200 mb-4">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li class="me-2">
                    <a href="#" x-on:click.prevent="tab = 'consulta'"
                       :class="{
                           'text-blue-600 border-blue-600': tab === 'consulta',
                           'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'consulta'
                       }"
                       class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200">
                        <i class="fa-solid fa-stethoscope me-2"></i>
                        Consulta
                    </a>
                </li>
                <li class="me-2">
                    <a href="#" x-on:click.prevent="tab = 'receta'"
                       :class="{
                           'text-blue-600 border-blue-600': tab === 'receta',
                           'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'receta'
                       }"
                       class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200">
                        <i class="fa-solid fa-prescription me-2"></i>
                        Receta
                    </a>
                </li>
            </ul>
        </div>

        {{-- Pestaña Consulta --}}
        <div x-show="tab === 'consulta'">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                    <textarea wire:model="diagnosis" rows="4"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Describa el diagnóstico del paciente aquí..."></textarea>
                    @error('diagnosis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tratamiento</label>
                    <textarea wire:model="treatment" rows="4"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Describa el tratamiento recomendado aquí..."></textarea>
                    @error('treatment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea wire:model="notes" rows="4"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Agregue notas adicionales sobre la consulta..."></textarea>
                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Pestaña Receta --}}
        <div x-show="tab === 'receta'" style="display: none;">
            <div class="space-y-4">
                @foreach ($prescriptions as $index => $prescription)
                    <div class="grid grid-cols-12 gap-4 items-end" wire:key="prescription-{{ $index }}">
                        <div class="col-span-4">
                            @if ($loop->first)
                                <label class="block text-sm font-medium text-gray-700 mb-1">Medicamento</label>
                            @endif
                            <input type="text" wire:model="prescriptions.{{ $index }}.medication"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Ej: Amoxicilina 500mg">
                        </div>
                        <div class="col-span-3">
                            @if ($loop->first)
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dosis</label>
                            @endif
                            <input type="text" wire:model="prescriptions.{{ $index }}.dose"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Ej: 1 cada 8 horas">
                        </div>
                        <div class="col-span-4">
                            @if ($loop->first)
                                <label class="block text-sm font-medium text-gray-700 mb-1">Frecuencia / Duración</label>
                            @endif
                            <input type="text" wire:model="prescriptions.{{ $index }}.frequency"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Ej: cada 8 horas por 7 días">
                        </div>
                        <div class="col-span-1">
                            @if (count($prescriptions) > 1)
                                <button type="button" wire:click="removeMedication({{ $index }})"
                                        class="w-full px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach

                <button type="button" wire:click="addMedication"
                        class="inline-flex items-center px-4 py-2 border border-blue-500 text-blue-500 rounded-lg hover:bg-blue-50 transition">
                    <i class="fa-solid fa-plus me-2"></i>
                    Añadir Medicamento
                </button>
            </div>
        </div>
    </div>

    {{-- Botón Guardar --}}
    <div class="flex justify-end mt-6">
        <x-wire-button wire:click="saveConsultation">
            <i class="fa-solid fa-floppy-disk me-2"></i>
            Guardar Consulta
        </x-wire-button>
    </div>
</div>
