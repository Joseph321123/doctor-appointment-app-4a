<?php

namespace App\Livewire\Admin\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Doctor;

class DoctorTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Doctor::query()->with(['user', 'speciality']);
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
            Column::make("Nombre", "user.name")
                ->sortable(),
            Column::make("Email", "user.email")
                ->sortable(),
            Column::make("Especialidad", "speciality.name")
                ->sortable()
                ->format(fn ($value) => $value ?? 'N/A'),
            Column::make("Licencia", "medical_license_number")
                ->sortable()
                ->format(fn ($value) => $value ?? 'N/A'),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.doctors.actions',
                        ['doctor' => $row]);
                })
        ];
    }
}
