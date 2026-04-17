<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransaksiExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters) {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Transaksi::with(['member', 'paket']);

        // Terapkan filter yang sama
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where('kode_invoice', 'like', "%{$search}%");
        }
        if (!empty($this->filters['status'])) $query->where('status', $this->filters['status']);
        if (!empty($this->filters['date_from'])) $query->whereDate('created_at', '>=', $this->filters['date_from']);
        if (!empty($this->filters['date_to'])) $query->whereDate('created_at', '<=', $this->filters['date_to']);

        return $query;
    }

    public function headings(): array {
        return ["Tanggal", "Invoice", "Pelanggan", "Tipe", "Channel", "Total", "Status"];
    }

    public function map($row): array {
        return [
            $row->created_at->format('d/m/Y'),
            $row->kode_invoice,
            $row->member->nama ?? $row->nama_tamu,
            $row->tipe,
            $row->channel,
            $row->jumlah_bayar,
            $row->status
        ];
    }
}
