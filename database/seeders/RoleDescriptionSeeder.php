<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Descripciones para los roles del sistema (datos de negocio).
 */
class RoleDescriptionSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Super Admin' => 'Acceso total al sistema y configuración.',
            'Paciente' => 'Usuario que agenda y recibe atención médica.',
            'Doctor' => 'Profesional que atiende citas y registra consultas.',
            'Recepcionista' => 'Gestión de citas y recepción en clínica.',
            'Administrador' => 'Reportes, usuarios y supervisión operativa.',
        ];

        foreach ($map as $name => $description) {
            DB::table('roles')
                ->where('name', $name)
                ->where('guard_name', 'web')
                ->update(['description' => $description]);
        }
    }
}
