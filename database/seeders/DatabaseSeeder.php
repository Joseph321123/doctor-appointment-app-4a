<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // llamar al RoleSeeder creado
        $this->call([
            RoleSeeder::class
        ]);

        //Crea un usuario de prueba cada que ejecute migrations
        //php artisan migrate:fresh --seed
        User::factory()->create([
            'name' => 'Joseph Aguilar',
            'email' => 'joseph@tecdesoftware.com.mx',
            'password' => bcrypt('123456'),
        ]);
    }
}
