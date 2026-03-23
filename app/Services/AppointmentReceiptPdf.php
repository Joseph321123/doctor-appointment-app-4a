<?php

namespace App\Services;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;

class AppointmentReceiptPdf
{
    public static function generate(Appointment $appointment): string
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'doctor.speciality']);

        $pdf = Pdf::loadView('pdfs.appointment-receipt', [
            'appointment' => $appointment,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->output();
    }
}
