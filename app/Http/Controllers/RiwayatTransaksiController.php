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
    // 1. Inisialisasi Query Dasar
    $query = Transaksi::with(['member', 'paket', 'verifikasi'])->latest();

    // --- BAGIAN FILTER (Sama seperti kode kamu) ---
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('kode_invoice', 'like', "%{$search}%")
                ->orWhere('nama_tamu', 'like', "%{$search}%")
                ->orWhereHas('member', fn($q) => $q->where('nama', 'like', "%{$search}%"));
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('date_from')) {
        $query->where('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->where('created_at', '<=', $request->date_to);
    }

    if ($request->filled('tipe')) {
        $query->where('tipe', $request->tipe);
    }

    if ($request->filled('channel')) {
        $query->where('channel', $request->channel);
    }
    // --- AKHIR BAGIAN FILTER ---

    // 2. LOGIC NOMINAL (Clone query agar filter tetap sinkron)
    // Kita hitung total hanya untuk yang statusnya 'dibayar' agar nominalnya valid sebagai pendapatan
    $totalNominal = (clone $query)->where('status', 'dibayar')->sum('jumlah_bayar');

    // 3. Eksekusi Paginate
    $data = $query->paginate(15)->withQueryString();

    // 4. Kirim totalNominal ke view
    return view('riwayat.index', compact('data', 'totalNominal'));
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

    // Samakan logic filter
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
    
    // 1. Buat nama file dinamis
    $namaFile = 'riwayat-transaksi-' . now()->format('Y m d-His') . '.pdf';

    // 2. Gunakan variabel $namaFile di dalam method download()
    return $pdf->setPaper('a4', 'landscape')->download($namaFile);
}
}
