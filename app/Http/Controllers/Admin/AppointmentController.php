<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        return view('admin.appointments.index');
    }

    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with(['user', 'speciality'])->get();
        $specialities = Speciality::orderBy('name')->get();
        return view('admin.appointments.create', compact('patients', 'doctors', 'specialities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'reason' => 'nullable|string|max:1000',
        ]);

        $data['duration'] = 15;
        $data['status'] = 1;

        Appointment::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Cita creada correctamente',
            'text' => 'La cita ha sido registrada exitosamente'
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function show(Appointment $appointment)
    {
        return view('admin.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'reason' => 'nullable|string|max:1000',
            'status' => 'required|in:1,2,3',
        ]);

        $appointment->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Cita actualizada correctamente',
            'text' => 'La cita ha sido editada exitosamente'
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Cita eliminada correctamente',
            'text' => 'La cita ha sido eliminada exitosamente'
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function consultation(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user']);
        return view('admin.appointments.consultation', compact('appointment'));
    }
}
