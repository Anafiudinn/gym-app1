<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['member', 'paket'])
            ->where('status', 'dibayar');

        // 🔍 SEARCH (nama member / tamu)
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('member', function ($m) use ($request) {
                    $m->where('nama', 'like', '%' . $request->search . '%');
                })
                    ->orWhere('nama_tamu', 'like', '%' . $request->search . '%');
            });
        }

        // 📅 FILTER TANGGAL
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal_pembayaran', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }


        $data = $query->latest()->get();

        // 💰 TOTAL PEMASUKAN
        $total = $data->where('status', 'dibayar')->sum('jumlah_bayar');

        return view('laporan.index', compact('data', 'total'));
    }
    public function export(Request $request)
    {
        // Pastikan request dikirim agar filter (tanggal_awal, dsb) ikut terbawa
        return Excel::download(new LaporanExport($request), 'laporan-keuangan-' . now()->format('Y-m-d') . '.xlsx');
    }
}
