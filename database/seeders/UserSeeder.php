<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Crea un usuario de prueba cada que ejecute migrations
        //php artisan migrate:fresh --seed
        User::factory()->create([
            'name' => 'Joseph Aguilar',
            'email' => 'joseph@tecdesoftware.com.mx',
            'password' => bcrypt('123456'),
            'id_number' => '123456789',
            'phone' => '5555555555',
            'address' => 'Calle 123, Colonia 456',
        ])->assignRole('Doctor');
    }
}
