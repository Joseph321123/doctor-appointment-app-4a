<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            'Cardiología',
            'Pediatría',
            'Dermatología',
            'Neurología',
            'Ortopedia',
            'Ginecología',
            'Oftalmología',
            'Psiquiatría',
            'Oncología',
            'Medicina General',
        ];

        foreach ($specialities as $name) {
            Speciality::firstOrCreate(['name' => $name]);
        }
    }
}
