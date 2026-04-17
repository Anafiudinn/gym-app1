<?php

namespace App\Http\Controllers;

use App\Exports\TransaksiExport;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatTransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Tampilkan SEMUA (Onsite & Online) yang sudah dibayar atau ditolak
        $query = Transaksi::with(['member', 'paket', 'verifikasi'])->latest();

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_invoice', 'like', "%{$search}%")
                    ->orWhere('nama_tamu', 'like', "%{$search}%")
                    ->orWhereHas('member', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter Channel (Biar bisa lihat mana yang bayar di kasir vs transfer)
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        $data = $query->paginate(10)->withQueryString();

        return view('riwayat.index', compact('data'));
    }
    //export excel
    public function exportExcel(Request $request)
    {
        // Ambil parameter filter dari URL
        $filters = $request->only(['search', 'status', 'date_from', 'date_to', 'channel']);

        // Download menggunakan class Export (kita buat di langkah 2)
        return Excel::download(new TransaksiExport($filters), 'riwayat-transaksi-' . now()->format('d-m-Y') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Transaksi::with(['member', 'paket'])->latest();

        // Samakan logic filter dengan index agar yang keluar sesuai pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_invoice', 'like', "%{$search}%")
                    ->orWhereHas('member', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $data = $query->get();

        $pdf = Pdf::loadView('riwayat.pdf', compact('data'));
        // Set paper ke A4 landscape biar kolomnya muat banyak
        return $pdf->setPaper('a4', 'landscape')->download('riwayat-transaksi.pdf');
    }
}
