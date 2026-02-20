<?php

use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;
// Gestión de usuarios
use App\Http\Controllers\Admin\UserController;
Route::resource('users', UserController::class);


Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

//Gestion de roles
Route::resource('roles', RoleController::class);

//Gestion de usuarios
Route::resource('users', UserController::class);

//Gestion de pacientes
Route::resource('patients', PatientController::class);

//Gestion de Doctores
Route::resource('doctors', DoctorController::class);
