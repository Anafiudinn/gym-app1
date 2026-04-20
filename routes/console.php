<?php

use App\Models\Transaksi;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Transaksi::where('status', 'pending')
        ->whereNotNull('expired_at')
        ->where('expired_at', '<', now())
        ->update(['status' => 'ditolak']);
})->everyMinute(); // Kita set setiap menit

Schedule::command('member:check-expired')->dailyAt('08:00');
