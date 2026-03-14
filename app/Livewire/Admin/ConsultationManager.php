<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Consultation;
use Livewire\Component;

class ConsultationManager extends Component
{
    public Appointment $appointment;

    public string $diagnosis = '';
    public string $treatment = '';
    public string $notes = '';
    public array $prescriptions = [];
    public bool $showPreviousConsultations = false;

    public function mount(Appointment $appointment): void
    {
        $this->appointment = $appointment->load(['patient.user', 'doctor.user']);

        $existing = $appointment->consultation;
        if ($existing) {
            $this->diagnosis = $existing->diagnosis ?? '';
            $this->treatment = $existing->treatment ?? '';
            $this->notes = $existing->notes ?? '';
            $this->prescriptions = $existing->prescriptions ?? [];
        }

        if (empty($this->prescriptions)) {
            $this->prescriptions = [['medication' => '', 'dose' => '', 'frequency' => '']];
        }
    }

    public function addMedication(): void
    {
        $this->prescriptions[] = ['medication' => '', 'dose' => '', 'frequency' => ''];
    }

    public function removeMedication(int $index): void
    {
        unset($this->prescriptions[$index]);
        $this->prescriptions = array_values($this->prescriptions);
    }

    public function saveConsultation(): void
    {
        $this->validate([
            'diagnosis' => 'nullable|string|max:2000',
            'treatment' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'prescriptions.*.medication' => 'nullable|string|max:255',
            'prescriptions.*.dose' => 'nullable|string|max:255',
            'prescriptions.*.frequency' => 'nullable|string|max:255',
        ]);

        $filteredPrescriptions = collect($this->prescriptions)
            ->filter(fn ($p) => !empty($p['medication']))
            ->values()
            ->toArray();

        Consultation::updateOrCreate(
            ['appointment_id' => $this->appointment->id],
            [
                'patient_id' => $this->appointment->patient_id,
                'doctor_id' => $this->appointment->doctor_id,
                'diagnosis' => $this->diagnosis,
                'treatment' => $this->treatment,
                'notes' => $this->notes,
                'prescriptions' => $filteredPrescriptions,
            ]
        );

        $this->appointment->update(['status' => 2]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Consulta guardada correctamente',
            'text' => 'La consulta ha sido registrada exitosamente'
        ]);

        $this->redirect(route('admin.appointments.index'));
    }

    public function getPreviousConsultationsProperty()
    {
        return Consultation::where('patient_id', $this->appointment->patient_id)
            ->where('appointment_id', '!=', $this->appointment->id)
            ->with(['doctor.user', 'appointment'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.consultation-manager');
    }
}
