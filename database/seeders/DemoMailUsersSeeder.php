<?php

namespace Database\Seeders;

use App\Models\BloodType;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Tres usuarios con correos distintos (Gmail +alias → mismo buzón soloinglesupy@gmail.com).
 */
class DemoMailUsersSeeder extends Seeder
{
    public function run(): void
    {
        $blood = BloodType::query()->first();
        $spec = Speciality::query()->where('name', 'Medicina General')->first()
            ?? Speciality::query()->first();

        $admin = User::firstOrCreate(
            ['email' => 'soloinglesupy+admin@gmail.com'],
            [
                'name' => 'Administración Demo',
                'password' => '123456',
                'id_number' => 'ADM-DEMO-01',
                'phone' => '5551000001',
                'address' => 'Oficina central',
            ]
        );
        if (! $admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
        }

        $patientUser = User::firstOrCreate(
            ['email' => 'soloinglesupy+paciente@gmail.com'],
            [
                'name' => 'Paciente Demo',
                'password' => '123456',
                'id_number' => 'PAC-DEMO-01',
                'phone' => '5552000002',
                'address' => 'Ciudad de México',
            ]
        );
        if (! $patientUser->hasRole('Paciente')) {
            $patientUser->assignRole('Paciente');
        }
        if (! $patientUser->patient) {
            Patient::create([
                'user_id' => $patientUser->id,
                'blood_type_id' => $blood?->id,
                'allergies' => null,
                'chronic_conditions' => null,
                'surgical_history' => null,
                'family_history' => null,
                'observations' => 'Cuenta demo paciente (+paciente).',
                'emergency_contact_name' => 'Contacto demo',
                'emergency_contact_phone' => '5550000000',
                'emergency_contact_relationship' => 'Familiar',
            ]);
        }

        $doctorUser = User::firstOrCreate(
            ['email' => 'soloinglesupy+doctor@gmail.com'],
            [
                'name' => 'Dr. Demo Médico',
                'password' => '123456',
                'id_number' => 'DOC-DEMO-01',
                'phone' => '5553000003',
                'address' => 'Hospital demo',
            ]
        );
        if (! $doctorUser->hasRole('Doctor')) {
            $doctorUser->assignRole('Doctor');
        }
        if (! $doctorUser->doctor) {
            Doctor::create([
                'user_id' => $doctorUser->id,
                'speciality_id' => $spec?->id,
                'medical_license_number' => '7654321',
                'biography' => 'Doctor demo para comprobantes (+doctor).',
            ]);
        }
    }
}
