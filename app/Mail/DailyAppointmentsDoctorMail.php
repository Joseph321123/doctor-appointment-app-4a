<?php

namespace App\Mail;

use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyAppointmentsDoctorMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Doctor $doctor,
        public Collection $appointments,
        public Carbon $date
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tus citas para el '.$this->date->format('d/m/Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reports.daily-doctor',
        );
    }
}
