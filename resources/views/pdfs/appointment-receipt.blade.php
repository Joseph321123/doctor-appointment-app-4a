<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 18px; margin-bottom: 4px; color: #1f2937; }
        .muted { color: #6b7280; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; }
        th { background: #f3f4f6; }
        .footer { margin-top: 24px; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Comprobante de cita médica</h1>
    <p class="muted">{{ config('app.name') }}</p>

    <table>
        <tr>
            <th>Paciente</th>
            <td>{{ $appointment->patient->user->name }}
                @if($appointment->patient->user->email)
                    <br><span class="muted">{{ $appointment->patient->user->email }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Doctor</th>
            <td>{{ $appointment->doctor->user->name }}
                @if($appointment->doctor->speciality)
                    <br><span class="muted">{{ $appointment->doctor->speciality->name }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Fecha</th>
            <td>{{ $appointment->date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Horario</th>
            <td>{{ \Illuminate\Support\Str::substr($appointment->start_time, 0, 5) }} – {{ \Illuminate\Support\Str::substr($appointment->end_time, 0, 5) }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $appointment->status_label }}</td>
        </tr>
        @if($appointment->reason)
        <tr>
            <th>Motivo</th>
            <td>{{ $appointment->reason }}</td>
        </tr>
        @endif
    </table>

    <p class="footer">Documento generado automáticamente. Cita #{{ $appointment->id }}</p>
</body>
</html>
