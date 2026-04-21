<?php

namespace App\Http\View\Composers;

use App\Models\Setting;
use App\Models\VerifikasiPembayaran;
use App\Models\WaLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view): void
    {
        $pendingCount = Cache::remember('sidebar_pending_count', 60, function () {
            return VerifikasiPembayaran::where('status', 'pending')->count();
        });

        $failedWaCount = Cache::remember('sidebar_wa_failed_count', 60, function () {
            return WaLog::where('status', 'failed')->count();
        });

        $namaGym = Cache::remember('setting_nama_gym', 3600, function () {
            return Setting::getValue('nama_gym');
        });

        $view->with([
            'pendingCount'  => $pendingCount,
            'failedWaCount' => $failedWaCount,
            'namaGym'       => $namaGym,
        ]);
    }
}