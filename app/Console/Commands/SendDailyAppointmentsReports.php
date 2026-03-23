<?php

namespace App\Console\Commands;

use App\Mail\DailyAppointmentsAdminMail;
use App\Mail\DailyAppointmentsDoctorMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyAppointmentsReports extends Command
{
    protected $signature = 'appointments:send-daily-reports';

    protected $description = 'Envía reportes de citas del día a administradores y a cada doctor con agenda';

    public function handle(): int
    {
        $today = Carbon::today(config('app.timezone'));

        $appointments = Appointment::query()
            ->whereDate('date', $today)
            ->with(['patient.user', 'doctor.user'])
            ->orderBy('start_time')
            ->get();

        $admins = User::role('Administrador')->get();

        $needDelay = false;
        foreach ($admins as $admin) {
            if ($needDelay) {
                sleep(1);
            }
            try {
                Mail::to($admin->email)->send(new DailyAppointmentsAdminMail($appointments, $today));
            } catch (\Throwable $e) {
                Log::error('Daily admin report mail failed', [
                    'admin' => $admin->email,
                    'message' => $e->getMessage(),
                ]);
            }
            $needDelay = true;
        }

        $doctorIds = $appointments->pluck('doctor_id')->unique()->filter();

        foreach ($doctorIds as $doctorId) {
            $doctor = Doctor::with('user')->find($doctorId);
            if (! $doctor || ! $doctor->user?->email) {
                continue;
            }

            $forDoctor = $appointments->where('doctor_id', $doctorId)->values();

            if ($needDelay) {
                sleep(1);
            }
            try {
                Mail::to($doctor->user->email)->send(new DailyAppointmentsDoctorMail($doctor, $forDoctor, $today));
            } catch (\Throwable $e) {
                Log::error('Daily doctor report mail failed', [
                    'doctor' => $doctor->user->email,
                    'message' => $e->getMessage(),
                ]);
            }
            $needDelay = true;
        }

        $this->info('Reportes diarios enviados (admin '.$admins->count().', doctores '.$doctorIds->count().').');

        return self::SUCCESS;
    }
}
