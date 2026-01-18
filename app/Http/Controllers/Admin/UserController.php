<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'id_number' => 'required|string|min:5|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:users',
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:3|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create($data);

        $user->roles()->attach($data['role_id']);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario creado correctamente',
            'text' => 'El usuario ha sido creado exitosamente'
        ]);

        return redirect()->route('admin.users.index')->with('success','User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario actualizado correctamente',
            'text' => 'El usuario se ha editado correctamente'
        ]);

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'No se puede eliminar este usuario',
                'text' => 'No puedes eliminar tu propio usuario.'
            ]);
            return redirect()->route('admin.users.index');
        }

        $user->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario eliminado correctamente',
            'text' => 'El usuario ha sido eliminado exitosamente'
        ]);

        return redirect()->route('admin.users.index');
    }
}

