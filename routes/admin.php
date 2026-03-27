<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ImportPacientesController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Gestion de roles
    Route::resource('roles', RoleController::class);

    // Gestion de usuarios
    Route::resource('users', UserController::class);

    // Gestion de pacientes
    Route::resource('patients', PatientController::class);

    // Gestion de Doctores
    Route::resource('doctors', DoctorController::class);
    Route::get('doctors/{doctor}/schedules', [DoctorController::class, 'schedules'])->name('doctors.schedules');

    // Gestion de Citas
    Route::resource('appointments', AppointmentController::class);
    Route::get('appointments/{appointment}/consultation', [AppointmentController::class, 'consultation'])->name('appointments.consultation');
    Route::post('appointments/{appointment}/consultation', [AppointmentController::class, 'storeConsultation'])->name('appointments.consultation.store');

    // Importación masiva de pacientes (cola)
    Route::get('import-pacientes', [ImportPacientesController::class, 'index'])->name('import-pacientes.index');
    Route::post('import-pacientes', [ImportPacientesController::class, 'store'])->name('import-pacientes.store');
});
