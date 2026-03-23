<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Reportes diarios de citas (admin + doctores).
| Pruebas locales: cada minuto. En producción cambiar a:
| Schedule::command('appointments:send-daily-reports')->dailyAt('08:00');
*/
Schedule::command('appointments:send-daily-reports')->everyMinute();
