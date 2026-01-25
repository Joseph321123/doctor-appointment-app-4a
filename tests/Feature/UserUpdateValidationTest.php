<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('No se puede actualizar un usuario con datos inválidos', function () {

    $role = Role::forceCreate([
        'name' => 'Admin',
        'guard_name' => 'web',
    ]);

    $authUser = User::factory()->create();
    $this->actingAs($authUser);

    $user = User::factory()->create([
        'name' => 'Nombre Original'
    ]);

    $data = [
        'name' => '', // inválido
        'email' => 'correo-no-valido',
        'id_number' => '***',
        'phone' => '123',
        'address' => '',
        'role_id' => $role->id,
    ];

    $response = $this->put(route('admin.users.update', $user), $data);

    $response->assertSessionHasErrors();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Nombre Original',
    ]);
});
