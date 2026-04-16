<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaksi;
use App\Models\Absensi;
use App\Models\VerifikasiPembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // ── Pendapatan ────────────────────────────────────────
        $pendapatanHariIni = Transaksi::where('status', 'dibayar')
            ->whereDate('tanggal_pembayaran', $today)
            ->sum('jumlah_bayar');

        $pendapatanKemarin = Transaksi::where('status', 'dibayar')
            ->whereDate('tanggal_pembayaran', $yesterday)
            ->sum('jumlah_bayar');

        // Growth % vs kemarin
        $growthPendapatan = $pendapatanKemarin > 0
            ? round((($pendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100)
            : ($pendapatanHariIni > 0 ? 100 : 0);

        // ── Member ────────────────────────────────────────────
        $totalMember   = Member::count();
        $memberAktif   = Member::where('status', 'aktif')
                            ->where('tanggal_kadaluarsa', '>=', $today)
                            ->count();
        $memberExpired = Member::where(function ($q) use ($today) {
                            $q->where('status', 'expired')
                              ->orWhere('tanggal_kadaluarsa', '<', $today);
                        })->count();
        $memberLainnya = $totalMember - $memberAktif - $memberExpired;
        $memberLainnya = max($memberLainnya, 0);

        // ── Kunjungan ─────────────────────────────────────────
        $kunjunganHariIni = Absensi::whereDate('created_at', $today)->count();
        $kunjunganKemarin = Absensi::whereDate('created_at', $yesterday)->count();

        // ── Pending Verifikasi ────────────────────────────────
        $pendingVerifikasi = VerifikasiPembayaran::where('status', 'pending')->count();

        // ── Grafik 7 Hari ─────────────────────────────────────
        $label7Hari       = [];
        $pendapatan7Hari  = [];
        $kunjungan7Hari   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $label7Hari[]      = $date->format('d/m');
            $pendapatan7Hari[] = Transaksi::where('status', 'dibayar')
                ->whereDate('tanggal_pembayaran', $date)
                ->sum('jumlah_bayar');
            $kunjungan7Hari[]  = Absensi::whereDate('created_at', $date)->count();
        }

        $totalPendapatan7Hari = array_sum($pendapatan7Hari);

        // ── Transaksi Terbaru ─────────────────────────────────
        $transaksiTerbaru = Transaksi::with('member')
            ->latest()
            ->take(6)
            ->get();

        // ── Member Hampir Expired (7 hari ke depan) ───────────
        $memberHampirExpired = Member::where('status', 'aktif')
            ->whereBetween('tanggal_kadaluarsa', [$today, Carbon::today()->addDays(7)])
            ->orderBy('tanggal_kadaluarsa')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'pendapatanHariIni',
            'pendapatanKemarin',
            'growthPendapatan',
            'totalMember',
            'memberAktif',
            'memberExpired',
            'memberLainnya',
            'kunjunganHariIni',
            'kunjunganKemarin',
            'pendingVerifikasi',
            'label7Hari',
            'pendapatan7Hari',
            'kunjungan7Hari',
            'totalPendapatan7Hari',
            'transaksiTerbaru',
            'memberHampirExpired',
        ));
    }
}