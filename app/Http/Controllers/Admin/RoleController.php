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
    public function edit(string $id)
    {
        return view('admin.roles.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
