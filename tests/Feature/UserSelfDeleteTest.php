<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Usar la funcion para refrescar BD
uses(RefreshDatabase::class);

test('Un usuario no puede eliminarse a si mismo', function () {
    // 1) Crear un usuario de prueba
    $user = User::factory()->create();

    // 2) Simular que ese usuario ya inicio sesion
    $this->actingAs($user, "web");

    // 3) Simular una peticion HTTP DELETE (borrar un usuario)
    $response = $this->delete(route("admin.users.destroy", $user));

    // 4) Esperar que el servidor bloquee el borrado asi mismo
    $response->assertStatus(403);


    // 5) Verificar en la BD que sigue existiendo el usuario
    $this->assertDatabaseHas("users", [
        'id' => $user->id,
    ]);
});
