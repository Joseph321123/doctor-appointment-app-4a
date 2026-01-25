<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Schema::create('role_user', function ($table) {
        $table->foreignId('role_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    });
});

test('Al eliminar un usuario se eliminan sus roles asociados', function () {

    $authUser = User::factory()->create();
    $this->actingAs($authUser);

    $role = Role::forceCreate([
        'name' => 'Admin',
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->roles()->attach($role->id);

    $response = $this->delete(route('admin.users.destroy', $user));

    $response->assertStatus(302);

    // El usuario ya no debe existir
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);

    // Sus roles asociados tampoco
    $this->assertDatabaseMissing('role_user', [
        'user_id' => $user->id,
        'role_id' => $role->id,
    ]);
});

