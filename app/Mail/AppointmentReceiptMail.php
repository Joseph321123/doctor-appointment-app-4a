<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Services\AppointmentReceiptPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $recipientType // 'patient' | 'doctor' | 'admin'
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->recipientType) {
            'patient' => 'Comprobante de tu cita médica',
            'doctor' => 'Comprobante de cita médica (paciente)',
            default => 'Copia administración: nueva cita registrada',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-receipt',
            with: [
                'appointment' => $this->appointment,
                'recipientType' => $this->recipientType,
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = AppointmentReceiptPdf::generate($this->appointment);

        return [
            Attachment::fromData(fn () => $pdf, 'comprobante-cita-'.$this->appointment->id.'.pdf', [
                'mime' => 'application/pdf',
            ]),
        ];
    }
}
