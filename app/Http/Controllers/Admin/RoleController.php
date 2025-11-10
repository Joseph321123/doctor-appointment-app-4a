<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validar que se cree bien
        $request->validate(['name' => 'required|unique:roles,name']);

        //si pasa la validacion, crear el rol
        Role::create(['name' => $request->name]);

        //variable de un solo uso para alerta
        session()->flash('swal',
        [
            'icon' => 'success',
            'title' => 'rol creado correctamente',
            'text' => 'El rol ha sido creado exitosamente'
        ]
        );

        //redireccionara a la tabla principal
        return redirect()->route('admin.roles.index')
        ->with('success', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if($role->id <=4){
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'No se puede eliminar este rol.',
                'text' => 'Este rol es esencial para el sistema y no puede ser eliminado.',
            ]);

            return redirect()->route('admin.roles.index');
        }
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Validar edición correcta
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);

        // Si no hubo cambios
        if ($role->name === $request->name) {
            session()->flash('swal', [
                'icon' => 'info',
                'title' => 'Sin cambios.',
                'text' => 'No se detectaron modificaciones.',
            ]);

            return redirect()->route('admin.roles.edit', $role);
        }

        // Actualizar rol
        $role->update(['name' => $request->name]);

        // Alerta de éxito
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol actualizado correctamente.',
            'text' => 'El rol se ha editado correctamente.',
        ]);

        // Redireccionamiento a la tabla
        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->id <=4){
            //variable de un solo uso
            session()->flash('swal',
            [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se puede eliminar este rol.'
            ]
            );
            return redirect()->route('admin.roles.index');
        }

        //Borrar el elemento
        $role->delete();

        //Alerta
        session()->flash('swal',

            [
                'icon' => 'success',
                'title' => 'rol eliminado correctamente',
                'text' => 'El rol ha sido eliminado exitosamente'
            ]
        );

        //Redireccionar al mismo lugar
        return redirect()->route('admin.roles.index');
    }
}
