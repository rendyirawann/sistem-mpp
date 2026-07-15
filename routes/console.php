<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bersihkan foto wajah antrian online tiap Senin dini hari (minggu baru).
Schedule::command('antrian:bersihkan-wajah')->weeklyOn(1, '00:30');

// Sinkron hari libur nasional (Nager.Date) tiap awal bulan.
Schedule::command('libur:sync')->monthlyOn(1, '01:00');
