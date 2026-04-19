@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@push('styles')
<style>
    /* ── Card base ── */
    .card { background:#fff; border-radius:12px; border:1px solid #e9ecf0; overflow:hidden; }

    /* ── Section header ── */
    .section-header {
        display:flex; align-items:center; justify-content:space-between;
        padding:14px 18px; border-bottom:1px solid #f1f3f5;
    }
    .section-title { font-size:13px; font-weight:700; color:#111827; }
    .section-sub   { font-size:11px; color:#9ca3af; margin-top:1px; }

    /* ── Stat card ── */
    .stat-card {
        background:#fff; border:1px solid #e9ecf0; border-radius:12px;
        padding:18px 20px; transition:box-shadow .18s;
    }
    .stat-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.05); }
    .stat-icon {
        width:38px; height:38px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:15px; flex-shrink:0;
    }
    .stat-label { font-size:11px; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.06em; }
    .stat-value { font-size:24px; font-weight:800; color:#111827; line-height:1.1; margin-top:2px; }
    .stat-sub   { font-size:10.5px; color:#9ca3af; margin-top:4px; }

    /* ── Tab pills ── */
    .tab-bar { display:flex; gap:2px; background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:4px; }
    .tab-pill {
        padding:6px 14px; font-size:12px; font-weight:600;
        border-radius:7px; border:none; cursor:pointer;
        transition:all .15s; color:#6b7280; background:transparent;
    }
    .tab-pill:hover { background:#f3f4f6; color:#374151; }
    .tab-pill.active { background:#0f172a; color:#fff; }

    /* ── Filter form ── */
    .filter-input {
        border:1px solid #e5e7eb; border-radius:8px;
        padding:7px 11px; font-size:12px; color:#374151;
        background:#fff; outline:none;
        transition:border .15s, box-shadow .15s;
    }
    .filter-input:focus { border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.1); }

    /* ── Pane ── */
    .pane { display:none; }
    .pane.active { display:block; }

    /* ── Table ── */
    .tbl th {
        font-size:10.5px; font-weight:700; text-transform:uppercase;
        letter-spacing:.07em; color:#9ca3af;
        padding:10px 16px; border-bottom:1px solid #f1f3f5;
        white-space:nowrap;
    }
    .tbl td {
        padding:11px 16px; font-size:12.5px; color:#374151;
        border-bottom:1px solid #f8f9fa; vertical-align:middle;
    }
    .tbl tr:last-child td { border-bottom:none; }
    .tbl tr:hover td { background:#fafafa; }

    /* ── Rank row ── */
    .rank-row {
        display:flex; align-items:center; gap:12px;
        padding:11px 18px; border-bottom:1px solid #f8f9fa;
        transition:background .12s;
    }
    .rank-row:last-child { border-bottom:none; }
    .rank-row:hover { background:#fafafa; }

    .rank-num {
        width:22px; height:22px; border-radius:6px;
        font-size:11px; font-weight:800;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .rank-num.gold   { background:#fef9c3; color:#a16207; }
    .rank-num.silver { background:#f1f5f9; color:#475569; }
    .rank-num.bronze { background:#fff7ed; color:#c2410c; }
    .rank-num.plain  { background:#f3f4f6; color:#9ca3af; }

    .avatar-sm {
        width:32px; height:32px; border-radius:9px;
        background:#f0fdf7; color:#059669;
        font-size:12px; font-weight:700;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }

    /* ── Expired row ── */
    .expired-row {
        display:flex; align-items:center; gap:12px;
        padding:11px 18px; border-bottom:1px solid #f8f9fa;
        transition:background .12s;
    }
    .expired-row:last-child { border-bottom:none; }
    .expired-row:hover { background:#fffbeb; }

    .days-badge {
        font-size:10px; font-weight:700;
        padding:3px 9px; border-radius:6px; flex-shrink:0;
    }
    .days-badge.urgent { background:#fee2e2; color:#dc2626; }
    .days-badge.warn   { background:#fef3c7; color:#d97706; }
    .days-badge.ok     { background:#dcfce7; color:#16a34a; }

    /* ── Progress bar ── */
    .progress-bar { height:5px; border-radius:99px; background:#f1f5f9; overflow:hidden; }
    .progress-fill { height:100%; border-radius:99px; background:#10b981; transition:width .5s ease; }

    /* ── Breakdown chip ── */
    .breakdown-chip {
        background:#f8fafc; border-radius:10px;
        padding:12px 14px; border:1px solid #f1f3f5;
    }

    /* ── Filter button ── */
    .btn-filter {
        display:inline-flex; align-items:center; gap:6px;
        background:#0f172a; color:#fff;
        font-size:12px; font-weight:600;
        padding:7px 16px; border-radius:8px;
        border:none; cursor:pointer; transition:background .15s;
    }
    .btn-filter:hover { background:#1e293b; }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-[17px] font-bold text-gray-800 leading-tight">Laporan</h1>
        <p class="text-[12px] text-gray-400 mt-0.5">Analitik keuangan, kehadiran, dan member gym Anda</p>
    </div>
</div>

{{-- ── Filter Bar ── --}}
<div class="bg-white rounded-xl border border-gray-100 p-4 mb-5">
    <form action="{{ route('laporan.index') }}" method="GET"
          class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-[10.5px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="{{ $from }}" class="filter-input">
        </div>
        <div>
            <label class="block text-[10.5px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ $to }}" class="filter-input">
        </div>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-sliders text-[11px]"></i> Terapkan Filter
        </button>
        <a href="{{ route('laporan.index') }}"
           class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-gray-400
                  hover:text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-50 transition">
            <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
        </a>
    </form>
</div>

{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">

    <div class="stat-card lg:col-span-1">
        <div class="flex items-start justify-between mb-3">
            <div class="stat-icon bg-emerald-50">
                <i class="fa-solid fa-sack-dollar text-emerald-500"></i>
            </div>
            <span class="text-[9.5px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                Periode
            </span>
        </div>
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value text-[18px]">Rp{{ number_format($totalPendapatan,0,',','.') }}</div>
        <div class="stat-sub">{{ $transaksiTerbaru->count() }} transaksi tercatat</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-blue-50 mb-3">
            <i class="fa-solid fa-receipt text-blue-500"></i>
        </div>
        <div class="stat-label">Transaksi</div>
        <div class="stat-value">{{ $transaksiTerbaru->count() }}</div>
        <div class="stat-sub">transaksi dibayar</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-orange-50 mb-3">
            <i class="fa-solid fa-person-walking text-orange-400"></i>
        </div>
        <div class="stat-label">Tamu Harian</div>
        <div class="stat-value">{{ $totalTamuHarian }}</div>
        <div class="stat-sub">pengunjung harian</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-red-50 mb-3">
            <i class="fa-solid fa-triangle-exclamation text-red-400"></i>
        </div>
        <div class="stat-label">Akan Expired</div>
        <div class="stat-value">{{ $memberAkanExpired->count() }}</div>
        <div class="stat-sub">member dalam 7 hari</div>
    </div>

</div>

{{-- ── Tab Nav ── --}}
<div class="tab-bar mb-5 w-fit">
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


{{-- ════ PANE 1 — KEUANGAN ════ --}}
<div id="pane-keuangan" class="pane active">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Tabel Transaksi ── --}}
        <div class="card lg:col-span-2">
            <div class="section-header">
                <div>
                    <div class="section-title">Transaksi Terbaru</div>
                    <div class="section-sub">10 transaksi terakhir dalam periode</div>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                    {{ $transaksiTerbaru->count() }} data
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="tbl w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Invoice</th>
                            <th class="text-left">Pelanggan</th>
                            <th class="text-left">Paket</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTerbaru as $t)
                        <tr>
                            <td>
                                <div class="font-semibold text-gray-800 text-[12px]">{{ $t->kode_invoice }}</div>
                                <div class="text-[10.5px] text-gray-400">{{ $t->created_at->format('d M, H:i') }}</div>
                            </td>
                            <td>
                                <div class="font-semibold text-gray-700 text-[12.5px]">
                                    {{ $t->member->nama ?? $t->nama_tamu ?? '-' }}
                                </div>
                                <span class="text-[9.5px] font-bold uppercase px-2 py-0.5 rounded-full
                                    {{ $t->tipe === 'membership' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $t->tipe }}
                                </span>
                            </td>
                            <td class="text-gray-600">{{ $t->paket->nama_paket ?? 'Harian' }}</td>
                            <td class="text-right">
                                <div class="font-bold text-gray-800 text-[12.5px]">
                                    Rp{{ number_format($t->jumlah_bayar,0,',','.') }}
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

        {{-- Ringkasan Kanan ── --}}
        <div class="space-y-4">

            {{-- Breakdown per tipe ── --}}
            <div class="card">
                <div class="section-header">
                    <div>
                        <div class="section-title">Breakdown Tipe</div>
                        <div class="section-sub">Harian vs Membership</div>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    @php
                        $harianTotal = $transaksiTerbaru->where('tipe','harian')->sum('jumlah_bayar');
                        $memberTotal = $transaksiTerbaru->where('tipe','membership')->sum('jumlah_bayar');
                        $grandTotal  = $harianTotal + $memberTotal;
                        $harianPct   = $grandTotal > 0 ? round($harianTotal / $grandTotal * 100) : 0;
                        $memberPct   = 100 - $harianPct;
                    @endphp
                    <div>
                        <div class="flex justify-between text-[12px] mb-1.5">
                            <span class="font-semibold text-gray-700">
                                <i class="fa-solid fa-person-walking text-orange-400 mr-1 text-[10px]"></i> Tamu Harian
                            </span>
                            <span class="font-bold text-gray-900">{{ $harianPct }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width:{{ $harianPct }}%; background:#fb923c;"></div>
                        </div>
                        <div class="text-[10.5px] text-gray-400 mt-1">Rp{{ number_format($harianTotal,0,',','.') }}</div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[12px] mb-1.5">
                            <span class="font-semibold text-gray-700">
                                <i class="fa-solid fa-id-card text-blue-500 mr-1 text-[10px]"></i> Membership
                            </span>
                            <span class="font-bold text-gray-900">{{ $memberPct }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width:{{ $memberPct }}%; background:#3b82f6;"></div>
                        </div>
                        <div class="text-[10.5px] text-gray-400 mt-1">Rp{{ number_format($memberTotal,0,',','.') }}</div>
                    </div>
                </div>
            </div>

            {{-- Per Channel ── --}}
            <div class="card">
                <div class="section-header">
                    <div class="section-title">Per Channel</div>
                </div>
                <div class="p-5 space-y-3">
                    @php
                        $onsiteTotal = $transaksiTerbaru->where('channel','onsite')->sum('jumlah_bayar');
                        $onlineTotal = $transaksiTerbaru->where('channel','online')->sum('jumlah_bayar');
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="text-[12.5px] font-semibold text-gray-700">Onsite</span>
                        </div>
                        <span class="text-[12.5px] font-bold text-gray-900">Rp{{ number_format($onsiteTotal,0,',','.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                            <span class="text-[12.5px] font-semibold text-gray-700">Online</span>
                        </div>
                        <span class="text-[12.5px] font-bold text-gray-900">Rp{{ number_format($onlineTotal,0,',','.') }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                        <span class="text-[12px] font-bold text-gray-500">Total</span>
                        <span class="text-[13.5px] font-black text-emerald-600">Rp{{ number_format($totalPendapatan,0,',','.') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


{{-- ════ PANE 2 — KEHADIRAN ════ --}}
<div id="pane-kehadiran" class="pane">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Member Terajin ── --}}
        <div class="card">
            <div class="section-header">
                <div>
                    <div class="section-title">Member Terajin</div>
                    <div class="section-sub">Berdasarkan absensi pada periode ini</div>
                </div>
                <i class="fa-solid fa-trophy text-amber-400 text-[14px]"></i>
            </div>
            @forelse($memberTerajin as $i => $m)
            <div class="rank-row">
                <div class="rank-num {{ $i===0?'gold':($i===1?'silver':($i===2?'bronze':'plain')) }}">{{ $i+1 }}</div>
                <div class="avatar-sm">{{ strtoupper(substr($m->nama,0,1)) }}</div>
                <div class="flex-1 min-w-0">
                    <div class="text-[12.5px] font-semibold text-gray-800 truncate">{{ $m->nama }}</div>
                    <div class="text-[10.5px] text-gray-400">{{ $m->kode_member }}</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-[17px] font-black text-emerald-600 leading-none">{{ $m->absensi_count }}</div>
                    <div class="text-[9.5px] text-gray-400">kunjungan</div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-[12px] text-gray-400">
                <i class="fa-solid fa-calendar-xmark text-2xl mb-2 block text-gray-200"></i>
                Tidak ada data absensi pada periode ini
            </div>
            @endforelse
        </div>

        {{-- Statistik Kunjungan ── --}}
        <div class="space-y-4">

            <div class="card">
                <div class="section-header">
                    <div>
                        <div class="section-title">Ringkasan Kunjungan</div>
                        <div class="section-sub">Tamu harian vs member aktif</div>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-2 gap-3">
                    <div class="bg-orange-50 rounded-xl p-4 text-center">
                        <div class="text-[28px] font-black text-orange-500 leading-none">{{ $totalTamuHarian }}</div>
                        <div class="text-[10.5px] text-orange-600 font-semibold mt-1">Tamu Harian</div>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-4 text-center">
                        @php $totalAbsensi = $memberTerajin->sum('absensi_count'); @endphp
                        <div class="text-[28px] font-black text-emerald-600 leading-none">{{ $totalAbsensi }}</div>
                        <div class="text-[10.5px] text-emerald-700 font-semibold mt-1">Absensi Member</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="section-header">
                    <div class="section-title">Grafik Kunjungan Top 5</div>
                </div>
                <div class="p-5 space-y-3">
                    @php $maxCount = $memberTerajin->max('absensi_count') ?: 1; @endphp
                    @foreach($memberTerajin as $m)
                    <div class="flex items-center gap-3">
                        <span class="text-[11px] font-semibold text-gray-500 w-20 truncate flex-shrink-0">
                            {{ \Illuminate\Support\Str::limit($m->nama,10) }}
                        </span>
                        <div class="flex-1 progress-bar">
                            <div class="progress-fill" style="width:{{ round($m->absensi_count/$maxCount*100) }}%"></div>
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


{{-- ════ PANE 3 — MEMBER ════ --}}
<div id="pane-member" class="pane">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Member Loyal ── --}}
        <div class="card">
            <div class="section-header">
                <div>
                    <div class="section-title">Member Loyal</div>
                    <div class="section-sub">Terlama bergabung sejak awal</div>
                </div>
                <i class="fa-solid fa-heart text-red-400 text-[14px]"></i>
            </div>
            @forelse($memberLoyal as $i => $m)
            <div class="rank-row">
                <div class="rank-num {{ $i===0?'gold':($i===1?'silver':($i===2?'bronze':'plain')) }}">{{ $i+1 }}</div>
                <div class="avatar-sm" style="{{ $i===0 ? 'background:#fef9c3;color:#a16207;' : '' }}">
                    {{ strtoupper(substr($m->nama,0,1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[12.5px] font-semibold text-gray-800 truncate">{{ $m->nama }}</div>
                    <div class="text-[10.5px] text-gray-400">{{ $m->kode_member }}</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-[11px] font-bold text-gray-700">
                        {{ \Carbon\Carbon::parse($m->tanggal_daftar)->format('d M Y') }}
                    </div>
                    <div class="text-[9.5px] text-gray-400">
                        {{ \Carbon\Carbon::parse($m->tanggal_daftar)->diffForHumans(null,true) }} lalu
                    </div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-[12px] text-gray-400">
                <i class="fa-solid fa-users text-2xl mb-2 block text-gray-200"></i>
                Belum ada data member
            </div>
            @endforelse
        </div>

        {{-- Member Akan Expired ── --}}
        <div class="card">
            <div class="section-header">
                <div>
                    <div class="section-title">Segera Expired</div>
                    <div class="section-sub">Membership berakhir dalam 7 hari ke depan</div>
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
                $daysLeft  = $expDate ? now()->diffInDays($expDate,false) : null;
                $badgeClass = $daysLeft !== null ? ($daysLeft<=2?'urgent':($daysLeft<=4?'warn':'ok')) : 'ok';
            @endphp
            <div class="expired-row">
                <div class="avatar-sm" style="background:#fff7ed;color:#c2410c;">
                    {{ strtoupper(substr($m->nama,0,1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[12.5px] font-semibold text-gray-800 truncate">{{ $m->nama }}</div>
                    <div class="text-[10.5px] text-gray-400">
                        {{ $m->kode_member }}
                        @if($expDate) · Exp: {{ $expDate->format('d M Y') }} @endif
                    </div>
                </div>
                @if($daysLeft !== null)
                <span class="days-badge {{ $badgeClass }}">{{ $daysLeft }} hari</span>
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
    var PANES = ['keuangan','kehadiran','member'];
    function switchPane(pane) {
        PANES.forEach(function(p) {
            var el  = document.getElementById('pane-' + p);
            var tab = document.getElementById('tab-'  + p);
            if (!el || !tab) return;
            el.classList.toggle('active', p === pane);
            tab.classList.toggle('active', p === pane);
        });
    }
    window.switchPane = switchPane;
})();
</script>
@endpush