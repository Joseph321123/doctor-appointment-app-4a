<?php

namespace App\Livewire\Admin\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Appointment;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Appointment::query()->with(['patient.user', 'doctor.user']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Paciente", "patient.user.name")
                ->sortable()
                ->searchable(),
            Column::make("Doctor", "doctor.user.name")
                ->sortable()
                ->searchable(),
            Column::make("Fecha", "date")
                ->sortable()
                ->format(fn ($value) => $value->format('d/m/Y')),
            Column::make("Hora", "start_time")
                ->sortable(),
            Column::make("Hora Fin", "end_time")
                ->sortable(),
            Column::make("Estado")
                ->label(function ($row) {
                    return $row->status_label;
                }),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.appointments.actions',
                        ['appointment' => $row]);
                })
        ];
    }
}
