<?php

namespace Database\Seeders;

use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nombres de doctores reales de prueba
        $doctors = [
            ['name' => 'Dr. Carlos Mendoza',   'email' => 'carlos.mendoza@simify.com'],
            ['name' => 'Dra. Laura Pérez',      'email' => 'laura.perez@simify.com'],
            ['name' => 'Dr. Roberto Silva',     'email' => 'roberto.silva@simify.com'],
            ['name' => 'Dra. Ana García',       'email' => 'ana.garcia@simify.com'],
            ['name' => 'Dr. Jorge Ramírez',     'email' => 'jorge.ramirez@simify.com'],
        ];

        $specialities = Speciality::all();

        foreach ($doctors as $i => $data) {
            // firstOrCreate evita duplicados si el seeder se corre más de una vez
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => bcrypt('password'),
                    'id_number' => 'DOC-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'phone'    => '555' . str_pad($i + 1, 7, '0', STR_PAD_LEFT),
                    'address'  => 'Consultorio ' . ($i + 1) . ', Hospital Central',
                ]
            );

            if (!$user->hasRole('Doctor')) {
                $user->assignRole('Doctor');
            }

            // Crear el registro de doctor solo si no existe
            if (!$user->doctor) {
                $user->doctor()->create([
                    'speciality_id'          => $specialities->isNotEmpty()
                        ? $specialities[$i % $specialities->count()]->id
                        : null,
                    // Cédula profesional de 7 dígitos (formato DGP México)
                    'medical_license_number' => (string) rand(1000000, 9999999),
                    'biography'              => 'Médico especialista con amplia experiencia clínica.',
                ]);
            }
        }
    }
}
