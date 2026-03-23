<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyAppointmentsAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $appointments,
        public Carbon $date
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte diario: citas del '.$this->date->format('d/m/Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reports.daily-admin',
        );
    }
}
