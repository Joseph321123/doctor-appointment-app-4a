<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Un usuario autenticado puede actualizar un usuario', function () {

    $role = Role::forceCreate([
        'name' => 'Admin',
        'guard_name' => 'web',
    ]);

    $authUser = User::factory()->create();
    $this->actingAs($authUser);

    $user = User::factory()->create();

    $data = [
        'name' => 'Nombre Actualizado',
        'email' => 'actualizado@test.com',
        'id_number' => 'ID-99999',
        'phone' => '1234567890',
        'address' => 'Nueva dirección',
        'role_id' => $role->id,
    ];

    $response = $this->put(route('admin.users.update', $user), $data);

    $response->assertStatus(302);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Nombre Actualizado',
        'email' => 'actualizado@test.com',
    ]);
});



