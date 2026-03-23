<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #374151;">
    <p>Hola
        @if($recipientType === 'patient')
            {{ $appointment->patient->user->name }}
        @elseif($recipientType === 'doctor')
            {{ $appointment->doctor->user->name }}
        @else
            equipo de administración
        @endif
        ,
    </p>

    <p>
        @if($recipientType === 'patient')
            Se ha registrado tu cita médica. Adjuntamos el comprobante en PDF con los datos de la cita.
        @elseif($recipientType === 'doctor')
            Se ha registrado una nueva cita con uno de tus pacientes. Adjuntamos el comprobante en PDF.
        @else
            Se ha registrado una nueva cita en el sistema. Adjuntamos copia del comprobante en PDF para su registro.
        @endif
    </p>

    <p><strong>Paciente:</strong> {{ $appointment->patient->user->name }}</p>
    <p><strong>Doctor:</strong> {{ $appointment->doctor->user->name }}</p>
    <p><strong>Fecha:</strong> {{ $appointment->date->format('d/m/Y') }}</p>
    <p><strong>Horario:</strong> {{ \Illuminate\Support\Str::substr($appointment->start_time, 0, 5) }} – {{ \Illuminate\Support\Str::substr($appointment->end_time, 0, 5) }}</p>

    <p style="color: #9ca3af; font-size: 12px;">Saludos,<br>{{ config('app.name') }}</p>
</body>
</html>
