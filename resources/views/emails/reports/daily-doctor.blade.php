<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #374151;">
    <p>Hola {{ $doctor->user->name }},</p>

    <h2 style="margin-bottom: 8px;">Tus citas para {{ $date->format('d/m/Y') }}</h2>

    @if($appointments->isEmpty())
        <p>No tienes citas agendadas para este día.</p>
    @else
        <table style="width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px;">
            <thead>
                <tr style="background: #f3f4f6;">
                    <th style="border: 1px solid #e5e7eb; padding: 8px; text-align: left;">Hora</th>
                    <th style="border: 1px solid #e5e7eb; padding: 8px; text-align: left;">Paciente</th>
                    <th style="border: 1px solid #e5e7eb; padding: 8px; text-align: left;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 8px;">
                            {{ \Illuminate\Support\Str::substr($appointment->start_time, 0, 5) }} – {{ \Illuminate\Support\Str::substr($appointment->end_time, 0, 5) }}
                        </td>
                        <td style="border: 1px solid #e5e7eb; padding: 8px;">{{ $appointment->patient->user->name }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 8px;">{{ $appointment->status_label }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="color: #9ca3af; font-size: 12px; margin-top: 24px;">{{ config('app.name') }}</p>
</body>
</html>
