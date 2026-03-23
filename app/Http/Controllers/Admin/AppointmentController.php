<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentReceiptMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        $appointment = Appointment::create($data);
        $appointment->load(['patient.user', 'doctor.user', 'doctor.speciality']);

        $mailDriver = config('mail.default');
        $smtpUser = config('mail.mailers.smtp.username');
        $smtpPass = config('mail.mailers.smtp.password');
        $mailNote = '';
        $mailError = null;

        try {
            $smtpUrl = config('mail.mailers.smtp.url');
            if ($mailDriver === 'smtp' && empty($smtpUrl) && (empty($smtpUser) || $smtpPass === null || $smtpPass === '')) {
                throw new \RuntimeException(
                    'Configura MAIL_USERNAME y MAIL_PASSWORD en tu archivo .env para SMTP (Mailtrap), o MAIL_URL. Luego ejecuta: php artisan config:clear'
                );
            }

            $patientEmail = $appointment->patient->user->email ?? null;
            $doctorEmail = $appointment->doctor->user->email ?? null;

            $pacienteCfg = config('mail.appointment_notification_emails.paciente');
            $doctorCfg = config('mail.appointment_notification_emails.doctor');
            $adminCfg = config('mail.appointment_notification_emails.admin');

            $patientTo = filled($pacienteCfg) ? $pacienteCfg : $patientEmail;
            $doctorTo = filled($doctorCfg) ? $doctorCfg : $doctorEmail;
            $adminTo = filled($adminCfg)
                ? $adminCfg
                : User::role('Administrador')->orderBy('id')->value('email');

            $queue = [];
            if ($patientTo) {
                $queue[] = ['to' => $patientTo, 'type' => 'patient'];
            }
            if ($doctorTo) {
                $queue[] = ['to' => $doctorTo, 'type' => 'doctor'];
            }
            if ($adminTo) {
                $queue[] = ['to' => $adminTo, 'type' => 'admin'];
            }

            if ($queue === []) {
                Log::warning('Cita sin destinatarios de correo', [
                    'appointment_id' => $appointment->id,
                ]);
                $mailNote = ' No se enviaron correos: no hay emails de paciente, doctor ni administrador.';
            } else {
                foreach ($queue as $index => $item) {
                    if ($index > 0) {
                        sleep(10);
                    }
                    Mail::to($item['to'])->send(new AppointmentReceiptMail($appointment, $item['type']));
                }

                $sentCount = count($queue);
                if ($mailDriver === 'log') {
                    $mailNote = ' El correo está en modo LOG: revisa storage/logs/laravel.log. Para SMTP real: MAIL_MAILER=smtp y php artisan config:clear.';
                } elseif ($mailDriver === 'smtp') {
                    $mailNote = " Se enviaron {$sentCount} comprobante(s) por correo (10 s entre cada envío).";
                }
            }
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el comprobante de cita por correo', [
                'appointment_id' => $appointment->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $mailError = $e->getMessage();
        }

        $baseText = 'La cita ha sido registrada exitosamente.'.$mailNote;

        if ($mailError !== null) {
            session()->flash('swal', [
                'icon' => 'warning',
                'title' => 'Cita creada — aviso de correo',
                'text' => $baseText.' Error al enviar: '.$mailError,
            ]);
        } else {
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Cita creada correctamente',
                'text' => $baseText,
            ]);
        }

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
