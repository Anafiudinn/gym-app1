@extends('layouts.admin')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@push('styles')
<style>
    .card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #f0f0f0;
    }

    .stat-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #f0f0f0;
        padding: 20px 22px;
    }

    .stat-card.primary {
        background: #10b981;
        border-color: #10b981;
    }

    .form-input {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 8px 12px;
        font-size: 13px;
        color: #111827;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .form-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .form-label {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #9ca3af;
        margin-bottom: 5px;
    }

    .btn-filter {
        background: #10b981;
        color: #fff;
        font-size: 12.5px;
        font-weight: 700;
        padding: 9px 20px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: background 0.15s;
        white-space: nowrap;
    }

    .btn-filter:hover {
        background: #059669;
    }

    .btn-reset {
        background: #f3f4f6;
        color: #6b7280;
        padding: 9px 12px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .btn-reset:hover {
        background: #e5e7eb;
    }

    .th {
        padding: 11px 20px;
        text-align: left;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9ca3af;
        background: #fafafa;
        border-bottom: 1px solid #f3f4f6;
    }

    .td {
        padding: 12px 20px;
        border-bottom: 1px solid #f9fafb;
        vertical-align: middle;
    }

    tr:last-child .td {
        border-bottom: none;
    }

    tbody tr:hover td {
        background: #fafafa;
    }

    .tipe-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        background: #f3f4f6;
        color: #6b7280;
    }

    .print-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #374151;
        font-size: 12.5px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 9px;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }

    .print-btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    @media print {

        aside,
        header,
        .no-print {
            display: none !important;
        }

        .stat-card.primary {
            background: #fff !important;
            border: 2px solid #10b981 !important;
        }

        .stat-card.primary * {
            color: #111 !important;
        }
    }
</style>
@endpush

@section('content')

{{-- PAGE ACTIONS --}}
<div class="flex items-center justify-between mb-5">
    <a href="{{ route('laporan.export', request()->all()) }}" class="print-btn no-print">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Export Excel
    </a>
</div>



{{-- STAT CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">

    {{-- Total Pemasukan --}}
    <div class="stat-card primary">
        <p class="text-[10.5px] font-bold text-emerald-100 uppercase tracking-widest mb-1">Total Pemasukan</p>
        <h3 class="text-[26px] font-black text-white leading-tight">Rp{{ number_format($total, 0, ',', '.') }}</h3>
        <p class="text-[11px] text-emerald-200 mt-1">dari {{ $data->count() }} transaksi</p>
    </div>

    {{-- Total Transaksi --}}
    <div class="stat-card">
        <p class="text-[10.5px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jumlah Transaksi</p>
        <h3 class="text-[26px] font-black text-gray-800 leading-tight">{{ $data->count() }}</h3>
        <p class="text-[11px] text-gray-400 mt-1">record ditemukan</p>
    </div>

    {{-- Periode --}}
    <div class="stat-card">
        <p class="text-[10.5px] font-bold text-gray-400 uppercase tracking-widest mb-1">Periode Laporan</p>
        <h3 class="text-[16px] font-bold text-gray-800 mt-2">
            @if(request('tanggal_awal'))
            {{ date('d/m/Y', strtotime(request('tanggal_awal'))) }}
            <span class="text-gray-400 font-normal text-sm mx-1">—</span>
            {{ date('d/m/Y', strtotime(request('tanggal_akhir'))) }}
            @else
            <span class="text-gray-500 font-semibold">Semua Waktu</span>
            @endif
        </h3>
        @if(request('search'))
        <p class="text-[11px] text-gray-400 mt-1">Filter: "{{ request('search') }}"</p>
        @endif
    </div>

</div>
{{-- FILTER --}}
<div class="card p-5 mb-5 no-print">
    <form method="GET" action="/laporan" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="form-label">Cari Nama / Invoice</label>
            <input type="text" name="search" placeholder="Contoh: Budi..."
                value="{{ request('search') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="form-input">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-filter flex-1">Tampilkan</button>
            <a href="/laporan" class="btn-reset">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </a>
        </div>
    </form>
</div>

{{-- TABLE --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="th">Tanggal</th>
                    <th class="th">Pelanggan</th>
                    <th class="th text-center">Tipe</th>
                    <th class="th text-center">Paket</th>
                    <th class="th text-center">Channel</th>
                    <th class="th text-right">Jumlah</th>
                    <th class="th text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $d)
                <tr>
                    <td class="td">
                        <div class="text-[12.5px] font-semibold text-gray-700">
                            {{ $d->tanggal_pembayaran ? date('d M Y', strtotime($d->tanggal_pembayaran)) : '—' }}
                        </div>
                        <div class="text-[10.5px] font-mono text-gray-400 mt-0.5">{{ $d->kode_invoice ?? '—' }}</div>
                    </td>
                    <td class="td">
                        <span class="text-[13px] font-semibold text-gray-800">{{ $d->member->nama ?? $d->nama_tamu }}</span>
                    </td>
                    <td class="td text-center">
                        <span class="tipe-badge">{{ $d->tipe }}</span>
                    </td>
                    <td class="td text-center">
                        <span class="text-[12.5px] text-gray-600">{{ $d->paket->nama_paket ?? '—' }}</span>
                    </td>
                    <td class="td text-center">
                        <span class="text-[12.5px] text-gray-600">{{ $d->channel ?? '—' }}</span>
                    </td>
                    <td class="td text-right">
                        <span class="text-[13px] font-bold text-gray-900">Rp{{ number_format($d->jumlah_bayar, 0, ',', '.') }}</span>
                    </td>
                    <td class="td text-center">
                        @if(in_array($d->status, ['dibayar', 'Lunas']))
                        <span class="inline-flex items-center gap-1 text-[10.5px] font-bold uppercase text-emerald-600">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            Lunas
                        </span>
                        @else
                        <span class="text-[10.5px] font-semibold uppercase text-gray-400">{{ $d->status }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="td py-14 text-center text-[12px] text-gray-400 italic">
                        Tidak ada data untuk filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
            {{-- Footer Total --}}
            <tfoot>
                <tr class="border-t-2 border-gray-100 bg-gray-50/60">
                    <td colspan="4" class="px-5 py-4 text-right text-[11px] font-black text-gray-500 uppercase tracking-widest">
                        Grand Total
                    </td>
                    <td class="px-5 py-4 text-right">
                        <span class="text-[15px] font-black text-emerald-600">Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection