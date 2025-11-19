<?php

namespace App\Livewire\Admin\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;

class UserTable extends DataTableComponent
{
    //Se comenta para personalizar consultas
    //protected $model = User::class;

    //Define el  modelo y su consulta
    public function builder(): Builder
    {
        return User::query()->with('roles');
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
            Column::make("Nombre", "name")
                ->sortable(),
            Column::make("Email", "email")
                ->sortable(),
            Column::make("Numero de id", "id_number")
            ->sortable(),
            Column::make("Teléfono", "phone")
            ->sortable(),
            Column::make("Rol", "roles")
            ->label(function ($row) {
                return $row->roles->first()->name ?? 'Sin Rol';
            }),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.users.actions',
                        ['user' => $row]);

                })
        ];
    }
}
