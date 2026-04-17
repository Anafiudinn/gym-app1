@extends('layouts.admin')
@section('title', 'Laporan')
@section('page-title', 'Laporan')

@push('styles')
<style>
    .stat-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 20px 22px;
        transition: box-shadow 0.18s;
    }
    .stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.05); }

    .section-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        overflow: hidden;
    }

    .section-header {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-title {
        font-size: 12.5px;
        font-weight: 700;
        color: #1e293b;
    }

    .section-sub {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 1px;
    }

    .tab-pill {
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        color: #64748b;
        background: transparent;
    }
    .tab-pill:hover { background: #f1f5f9; color: #374151; }
    .tab-pill.active { background: #0f172a; color: #fff; }

    .form-input-sm {
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        padding: 7px 11px;
        font-size: 12px;
        color: #1e293b;
        background: #fff;
        outline: none;
        transition: border-color 0.15s;
    }
    .form-input-sm:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.08); }

    /* Bar chart */
    .bar-wrap {
        display: flex;
        align-items: flex-end;
        gap: 5px;
        height: 80px;
    }
    .bar-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    .bar-fill {
        width: 100%;
        border-radius: 5px 5px 0 0;
        background: #10b981;
        transition: height 0.4s ease;
        min-height: 3px;
    }
    .bar-label { font-size: 9px; color: #94a3b8; }

    /* Member rank rows */
    .rank-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 20px;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.12s;
    }
    .rank-row:last-child { border-bottom: none; }
    .rank-row:hover { background: #f8fafc; }

    .rank-num {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .rank-num.gold   { background: #fef9c3; color: #a16207; }
    .rank-num.silver { background: #f1f5f9; color: #475569; }
    .rank-num.bronze { background: #fff7ed; color: #c2410c; }
    .rank-num.plain  { background: #f8fafc; color: #94a3b8; }

    .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        background: #ecfdf5;
        color: #059669;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Expired warning */
    .expired-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 20px;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.12s;
    }
    .expired-row:last-child { border-bottom: none; }
    .expired-row:hover { background: #fffbeb; }

    .days-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        flex-shrink: 0;
    }
    .days-badge.urgent { background: #fee2e2; color: #dc2626; }
    .days-badge.warn   { background: #fef3c7; color: #d97706; }
    .days-badge.ok     { background: #dcfce7; color: #16a34a; }

    /* Transaksi tabel */
    .txn-table td, .txn-table th {
        padding: 11px 16px;
        font-size: 12px;
    }
    .txn-table thead tr { background: #f8fafc; }
    .txn-table tbody tr { border-bottom: 1px solid #f8fafc; transition: background 0.1s; }
    .txn-table tbody tr:last-child { border-bottom: none; }
    .txn-table tbody tr:hover { background: #f8fafc; }

    /* Animated counter */
    .counter { font-variant-numeric: tabular-nums; }

    /* Pane hidden */
    .pane { display: none; }
    .pane.active { display: block; }

    /* Progress bar */
    .progress-bar {
        height: 4px;
        border-radius: 4px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 4px;
        background: #10b981;
        transition: width 0.5s ease;
    }

    /* Filter button */
    .btn-filter {
        background: #0f172a;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 7px 16px;
        border-radius: 9px;
        border: none;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-filter:hover { background: #1e293b; }
</style>
@endpush

@section('content')

{{-- ─── FILTER BAR ─── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-4 mb-5">
    <form action="{{ route('laporan.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-[10.5px] font-700 text-gray-400 uppercase tracking-wider mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="{{ $from }}" class="form-input-sm" style="width:148px;">
        </div>
        <div>
            <label class="block text-[10.5px] font-700 text-gray-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ $to }}" class="form-input-sm" style="width:148px;">
        </div>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-sliders mr-1.5 text-[11px]"></i> Terapkan Filter
        </button>
        <a href="{{ route('laporan.index') }}"
           class="text-[12px] font-semibold text-gray-400 hover:text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
            Reset
        </a>
    </form>
</div>

{{-- ─── STAT CARDS ─── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

    {{-- Total Pendapatan --}}
    <div class="stat-card col-span-2 lg:col-span-1">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 bg-emerald-50 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-sack-dollar text-emerald-500 text-[14px]"></i>
            </div>
            <span class="text-[9.5px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                Periode
            </span>
        </div>
        <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Total Pendapatan</p>
        <p class="text-[22px] font-black text-gray-900 leading-none counter">
            Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
        </p>
        <p class="text-[10.5px] text-gray-400 mt-1.5">{{ $transaksiTerbaru->count() }} transaksi tercatat</p>
    </div>

    {{-- Total Transaksi --}}
    <div class="stat-card">
        <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center mb-3">
            <i class="fa-solid fa-receipt text-blue-500 text-[14px]"></i>
        </div>
        <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Transaksi</p>
        <p class="text-[26px] font-black text-gray-900 leading-none">{{ $transaksiTerbaru->count() }}</p>
        <p class="text-[10.5px] text-gray-400 mt-1.5">transaksi dibayar</p>
    </div>

    {{-- Tamu Harian --}}
    <div class="stat-card">
        <div class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center mb-3">
            <i class="fa-solid fa-person-walking text-orange-400 text-[14px]"></i>
        </div>
        <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Tamu Harian</p>
        <p class="text-[26px] font-black text-gray-900 leading-none">{{ $totalTamuHarian }}</p>
        <p class="text-[10.5px] text-gray-400 mt-1.5">pengunjung harian</p>
    </div>

    {{-- Akan Expired --}}
    <div class="stat-card">
        <div class="w-9 h-9 bg-red-50 rounded-xl flex items-center justify-center mb-3">
            <i class="fa-solid fa-triangle-exclamation text-red-400 text-[14px]"></i>
        </div>
        <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Akan Expired</p>
        <p class="text-[26px] font-black text-gray-900 leading-none">{{ $memberAkanExpired->count() }}</p>
        <p class="text-[10.5px] text-gray-400 mt-1.5">member dalam 7 hari</p>
    </div>
</div>

{{-- ─── TAB NAVIGASI ─── --}}
<div class="flex items-center gap-1 bg-white border border-gray-100 rounded-xl p-1.5 mb-5 w-fit">
    <button type="button" onclick="switchPane('keuangan')" id="tab-keuangan" class="tab-pill active">
        <i class="fa-solid fa-chart-line text-[10px] mr-1.5"></i>Keuangan
    </button>
    <button type="button" onclick="switchPane('kehadiran')" id="tab-kehadiran" class="tab-pill">
        <i class="fa-solid fa-calendar-check text-[10px] mr-1.5"></i>Kehadiran
    </button>
    <button type="button" onclick="switchPane('member')" id="tab-member" class="tab-pill">
        <i class="fa-solid fa-users text-[10px] mr-1.5"></i>Member
    </button>
</div>

{{-- ════════════════════════════════════════
     PANE 1 — KEUANGAN
════════════════════════════════════════ --}}
<div id="pane-keuangan" class="pane active">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Tabel Transaksi Terbaru --}}
        <div class="section-card lg:col-span-2">
            <div class="section-header">
                <div>
                    <p class="section-title">Transaksi Terbaru</p>
                    <p class="section-sub">10 transaksi terakhir dalam periode</p>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                    {{ $transaksiTerbaru->count() }} data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full txn-table">
                    <thead>
                        <tr>
                            <th class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Invoice</th>
                            <th class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Paket</th>
                            <th class="text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTerbaru as $t)
                        <tr>
                            <td>
                                <div class="font-semibold text-gray-800 text-[11.5px]">{{ $t->kode_invoice }}</div>
                                <div class="text-[10px] text-gray-400">{{ $t->created_at->format('d M, H:i') }}</div>
                            </td>
                            <td>
                                <div class="font-semibold text-gray-700 text-[12px]">
                                    {{ $t->member->nama ?? $t->nama_tamu ?? '-' }}
                                </div>
                                <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded
                                    {{ $t->tipe === 'membership' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $t->tipe }}
                                </span>
                            </td>
                            <td class="text-[12px] text-gray-600">
                                {{ $t->paket->nama_paket ?? 'Harian' }}
                            </td>
                            <td class="text-right">
                                <div class="font-bold text-gray-900 text-[12.5px]">
                                    Rp{{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                                </div>
                                <div class="text-[9.5px] font-semibold text-emerald-500 uppercase">dibayar</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-gray-400 text-[12px]">
                                <i class="fa-solid fa-receipt text-2xl mb-2 block text-gray-200"></i>
                                Tidak ada transaksi pada periode ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ringkasan Kanan --}}
        <div class="space-y-4">

            {{-- Breakdown per tipe --}}
            <div class="section-card">
                <div class="section-header">
                    <div>
                        <p class="section-title">Breakdown Tipe</p>
                        <p class="section-sub">Perbandingan harian vs membership</p>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    @php
                        $harianTotal = $transaksiTerbaru->where('tipe', 'harian')->sum('jumlah_bayar');
                        $memberTotal = $transaksiTerbaru->where('tipe', 'membership')->sum('jumlah_bayar');
                        $grandTotal  = $harianTotal + $memberTotal;
                        $harianPct   = $grandTotal > 0 ? round($harianTotal / $grandTotal * 100) : 0;
                        $memberPct   = 100 - $harianPct;
                    @endphp

                    <div>
                        <div class="flex justify-between text-[11.5px] mb-1.5">
                            <span class="font-semibold text-gray-700">
                                <i class="fa-solid fa-person-walking text-orange-400 mr-1 text-[10px]"></i> Tamu Harian
                            </span>
                            <span class="font-bold text-gray-900">{{ $harianPct }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $harianPct }}%; background: #fb923c;"></div>
                        </div>
                        <p class="text-[10.5px] text-gray-400 mt-1">Rp{{ number_format($harianTotal, 0, ',', '.') }}</p>
                    </div>

                    <div>
                        <div class="flex justify-between text-[11.5px] mb-1.5">
                            <span class="font-semibold text-gray-700">
                                <i class="fa-solid fa-id-card text-blue-500 mr-1 text-[10px]"></i> Membership
                            </span>
                            <span class="font-bold text-gray-900">{{ $memberPct }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $memberPct }}%; background: #3b82f6;"></div>
                        </div>
                        <p class="text-[10.5px] text-gray-400 mt-1">Rp{{ number_format($memberTotal, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Channel breakdown --}}
            <div class="section-card">
                <div class="section-header">
                    <p class="section-title">Per Channel</p>
                </div>
                <div class="p-5 space-y-3">
                    @php
                        $onsiteTotal = $transaksiTerbaru->where('channel', 'onsite')->sum('jumlah_bayar');
                        $onlineTotal = $transaksiTerbaru->where('channel', 'online')->sum('jumlah_bayar');
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                            <span class="text-[12px] font-semibold text-gray-700">Onsite</span>
                        </div>
                        <span class="text-[12px] font-bold text-gray-900">Rp{{ number_format($onsiteTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400 flex-shrink-0"></span>
                            <span class="text-[12px] font-semibold text-gray-700">Online</span>
                        </div>
                        <span class="text-[12px] font-bold text-gray-900">Rp{{ number_format($onlineTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-50 pt-3 flex items-center justify-between">
                        <span class="text-[11.5px] font-bold text-gray-500">Total</span>
                        <span class="text-[13px] font-black text-emerald-600">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     PANE 2 — KEHADIRAN
════════════════════════════════════════ --}}
<div id="pane-kehadiran" class="pane">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Member Terajin --}}
        <div class="section-card">
            <div class="section-header">
                <div>
                    <p class="section-title">Member Terajin</p>
                    <p class="section-sub">Berdasarkan absensi valid pada periode ini</p>
                </div>
                <i class="fa-solid fa-trophy text-amber-400 text-[14px]"></i>
            </div>

            @forelse($memberTerajin as $i => $m)
            <div class="rank-row">
                <div class="rank-num {{ $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : 'plain')) }}">
                    {{ $i + 1 }}
                </div>
                <div class="avatar-sm">{{ strtoupper(substr($m->nama, 0, 1)) }}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12.5px] font-semibold text-gray-800 truncate">{{ $m->nama }}</p>
                    <p class="text-[10.5px] text-gray-400">{{ $m->kode_member }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-[16px] font-black text-emerald-600 leading-none">{{ $m->absensi_count }}</p>
                    <p class="text-[9.5px] text-gray-400">kunjungan</p>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-[12px] text-gray-400">
                <i class="fa-solid fa-calendar-xmark text-2xl mb-2 block text-gray-200"></i>
                Tidak ada data absensi pada periode ini
            </div>
            @endforelse
        </div>

        {{-- Statistik Kunjungan --}}
        <div class="space-y-4">

            {{-- Total Tamu Summary --}}
            <div class="section-card">
                <div class="section-header">
                    <div>
                        <p class="section-title">Ringkasan Kunjungan</p>
                        <p class="section-sub">Tamu harian vs Member aktif</p>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    <div class="bg-orange-50 rounded-xl p-4 text-center">
                        <p class="text-[28px] font-black text-orange-500 leading-none">{{ $totalTamuHarian }}</p>
                        <p class="text-[10.5px] text-orange-600 font-semibold mt-1">Tamu Harian</p>
                        <p class="text-[10px] text-orange-400 mt-0.5">tiket harian</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-4 text-center">
                        @php $totalAbsensi = $memberTerajin->sum('absensi_count'); @endphp
                        <p class="text-[28px] font-black text-emerald-600 leading-none">{{ $totalAbsensi }}</p>
                        <p class="text-[10.5px] text-emerald-700 font-semibold mt-1">Absensi Member</p>
                        <p class="text-[10px] text-emerald-500 mt-0.5">check-in valid</p>
                    </div>
                </div>
            </div>

            {{-- Top member chart visual --}}
            <div class="section-card">
                <div class="section-header">
                    <p class="section-title">Grafik Kunjungan Top 5</p>
                </div>
                <div class="p-5">
                    @php $maxCount = $memberTerajin->max('absensi_count') ?: 1; @endphp
                    <div class="space-y-3">
                        @foreach($memberTerajin as $m)
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] font-semibold text-gray-500 w-20 truncate flex-shrink-0">
                                {{ \Illuminate\Support\Str::limit($m->nama, 10) }}
                            </span>
                            <div class="flex-1 progress-bar">
                                <div class="progress-fill" style="width: {{ $maxCount > 0 ? round($m->absensi_count / $maxCount * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-[11px] font-bold text-gray-800 w-5 text-right flex-shrink-0">
                                {{ $m->absensi_count }}
                            </span>
                        </div>
                        @endforeach
                        @if($memberTerajin->isEmpty())
                        <p class="text-[12px] text-gray-400 text-center py-4">Tidak ada data</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     PANE 3 — MEMBER
════════════════════════════════════════ --}}
<div id="pane-member" class="pane">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Member Loyal --}}
        <div class="section-card">
            <div class="section-header">
                <div>
                    <p class="section-title">Member Loyal</p>
                    <p class="section-sub">Terlama bergabung sejak awal</p>
                </div>
                <i class="fa-solid fa-heart text-red-400 text-[14px]"></i>
            </div>

            @forelse($memberLoyal as $i => $m)
            <div class="rank-row">
                <div class="rank-num {{ $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : 'plain')) }}">
                    {{ $i + 1 }}
                </div>
                <div class="avatar-sm" style="{{ $i === 0 ? 'background:#fef9c3;color:#a16207;' : '' }}">
                    {{ strtoupper(substr($m->nama, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12.5px] font-semibold text-gray-800 truncate">{{ $m->nama }}</p>
                    <p class="text-[10.5px] text-gray-400">{{ $m->kode_member }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-[11px] font-bold text-gray-700">
                        {{ \Carbon\Carbon::parse($m->tanggal_daftar)->format('d M Y') }}
                    </p>
                    <p class="text-[9.5px] text-gray-400">
                        {{ \Carbon\Carbon::parse($m->tanggal_daftar)->diffForHumans(null, true) }} lalu
                    </p>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-[12px] text-gray-400">
                <i class="fa-solid fa-users text-2xl mb-2 block text-gray-200"></i>
                Belum ada data member
            </div>
            @endforelse
        </div>

        {{-- Member Akan Expired --}}
        <div class="section-card">
            <div class="section-header">
                <div>
                    <p class="section-title">Segera Expired</p>
                    <p class="section-sub">Membership berakhir dalam 7 hari ke depan</p>
                </div>
                @if($memberAkanExpired->count() > 0)
                <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-full border border-red-100 animate-pulse">
                    {{ $memberAkanExpired->count() }} member
                </span>
                @endif
            </div>

            @forelse($memberAkanExpired as $m)
            @php
                $latestMs  = $m->membership->sortByDesc('tanggal_selesai')->first();
                $expDate   = $latestMs ? \Carbon\Carbon::parse($latestMs->tanggal_selesai) : null;
                $daysLeft  = $expDate ? now()->diffInDays($expDate, false) : null;
                $badgeClass = $daysLeft !== null
                    ? ($daysLeft <= 2 ? 'urgent' : ($daysLeft <= 4 ? 'warn' : 'ok'))
                    : 'ok';
            @endphp
            <div class="expired-row">
                <div class="avatar-sm" style="background:#fff7ed;color:#c2410c;">
                    {{ strtoupper(substr($m->nama, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12.5px] font-semibold text-gray-800 truncate">{{ $m->nama }}</p>
                    <p class="text-[10.5px] text-gray-400">
                        {{ $m->kode_member }}
                        @if($expDate)
                        · Exp: {{ $expDate->format('d M Y') }}
                        @endif
                    </p>
                </div>
                @if($daysLeft !== null)
                <span class="days-badge {{ $badgeClass }}">
                    {{ $daysLeft }} hari
                </span>
                @endif
            </div>
            @empty
            <div class="py-12 text-center text-[12px] text-gray-400">
                <i class="fa-solid fa-circle-check text-2xl mb-2 block text-emerald-200"></i>
                Tidak ada member yang akan expired
            </div>
            @endforelse
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    var PANES = ['keuangan', 'kehadiran', 'member'];

    function switchPane(pane) {
        PANES.forEach(function(p) {
            var el  = document.getElementById('pane-' + p);
            var tab = document.getElementById('tab-' + p);
            if (!el || !tab) return;
            el.classList.toggle('active', p === pane);
            tab.classList.toggle('active', p === pane);
        });
    }
    window.switchPane = switchPane;
})();
</script>
@endpush