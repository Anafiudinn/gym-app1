<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Member;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->endOfMonth()->toDateString();

        // --- TAB KEUANGAN ---
        $queryKeuangan = Transaksi::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->where('status', 'dibayar');

        $totalPendapatan = (clone $queryKeuangan)->sum('jumlah_bayar');
        $transaksiTerbaru = (clone $queryKeuangan)->with(['member', 'paket'])->latest()->take(10)->get();

        // --- TAB KEHADIRAN ---
        // 1. Member Terajin (Berdasarkan Absensi)
        $memberTerajin = Member::withCount(['absensi' => function ($q) use ($from, $to) {
            $q->whereBetween('waktu_masuk', [$from, $to])->where('status', 'valid');
        }])
            ->orderBy('absensi_count', 'desc')
            ->take(5)
            ->get();

        // 2. Total Tamu Harian (Dari Transaksi)
        $totalTamuHarian = Transaksi::where('tipe', 'Harian')
            ->where('status', 'dibayar')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->count();

        // --- TAB MEMBER ---
        // Member yang paling lama bergabung (Loyal)
        $memberLoyal = Member::orderBy('tanggal_daftar', 'asc')->take(5)->get();

        // Member yang akan expired dalam 7 hari
        $memberAkanExpired = Member::whereHas('membership', function ($q) {
            $q->whereBetween('tanggal_selesai', [now(), now()->addDays(7)]);
        })->with('membership')->get();

        return view('laporan.index', compact(
            'totalPendapatan',
            'transaksiTerbaru',
            'memberTerajin',
            'totalTamuHarian',
            'memberLoyal',
            'memberAkanExpired',
            'from',
            'to'
        ));
    }
}
