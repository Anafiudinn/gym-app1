<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Transaksi::with(['member', 'paket'])
            ->where('status', 'dibayar');

        // Filter Search
        if ($this->request->search) {
            $query->where(function ($q) {
                $q->whereHas('member', function ($m) {
                    $m->where('nama', 'like', '%' . $this->request->search . '%');
                })
                ->orWhere('nama_tamu', 'like', '%' . $this->request->search . '%');
            });
        }

        // Filter Tanggal
        if ($this->request->tanggal_awal && $this->request->tanggal_akhir) {
            $query->whereBetween('tanggal_pembayaran', [
                $this->request->tanggal_awal,
                $this->request->tanggal_akhir
            ]);
        }

        return $query->latest()->get();
    }

    // Menentukan Judul Kolom (Header)
    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pembayaran',
            'Invoice',
            'Nama Member/Tamu',
            'Tipe',
            'Paket',
            'Metode Pembayaran',
            'Total Bayar',
        ];
    }

    // Memetakan data agar yang muncul bukan ID, tapi nama
    public function map($transaksi): array
    {
        static $no = 1;
        return [
            $no++,
            \Carbon\Carbon::parse($transaksi->tanggal_pembayaran)->format('d-m-Y'),
            $transaksi->kode_invoice,
            $transaksi->tipe == 'member' ? ($transaksi->member->nama ?? '-') : $transaksi->nama_tamu,
            ucfirst($transaksi->tipe),
            $transaksi->paket->nama_paket ?? '-',
            strtoupper($transaksi->metode_pembayaran),
            $transaksi->jumlah_bayar,
        ];
    }
}