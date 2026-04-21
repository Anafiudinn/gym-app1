@extends('layouts.admin')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')

@push('styles')
<style>
    /* ── Card base ── */
    .card { background:#fff; border-radius:12px; border:1px solid #e9ecf0; }

    /* ── Stat card ── */
    .stat-card {
        background:#fff; border:1px solid #e9ecf0; border-radius:12px;
        padding:16px 18px; display:flex; align-items:center; gap:14px;
    }
    .stat-icon {
        width:40px; height:40px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:16px; flex-shrink:0;
    }
    .stat-label { font-size:11.5px; color:#9ca3af; font-weight:500; }
    .stat-value { font-size:20px; font-weight:800; color:#111827; line-height:1.1; margin-top:1px; }
    .stat-sub   { font-size:10.5px; color:#9ca3af; margin-top:3px; }

    /* ── Filter form inputs ── */
    .filter-input {
        width:100%; padding:7px 11px; border:1px solid #e5e7eb;
        border-radius:8px; font-size:12.5px; color:#374151;
        background:#f9fafb; outline:none;
        transition:border .15s, box-shadow .15s;
    }
    .filter-input:focus { border-color:#10b981; background:#fff; box-shadow:0 0 0 3px rgba(16,185,129,.1); }

    .filter-label {
        display:block; font-size:10.5px; font-weight:700;
        text-transform:uppercase; letter-spacing:.07em;
        color:#9ca3af; margin-bottom:4px;
    }

    /* ── Buttons ── */
    .btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:7px 14px; border-radius:8px;
        font-size:12px; font-weight:600;
        transition:all .15s; cursor:pointer; border:none;
        text-decoration:none; justify-content:center;
    }
    .btn-primary   { background:#10b981; color:#fff; }
    .btn-primary:hover { background:#059669; }
    .btn-dark      { background:#0f172a; color:#fff; }
    .btn-dark:hover { background:#1e293b; }
    .btn-white     { background:#fff; color:#374151; border:1px solid #e5e7eb; }
    .btn-white:hover { background:#f9fafb; }
    .btn-excel     { background:#f0fdf7; color:#059669; border:1px solid #bbf7d0; }
    .btn-excel:hover { background:#dcfce7; }
    .btn-pdf       { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .btn-pdf:hover { background:#fee2e2; }

    /* ── Table ── */
    .tbl th {
        font-size:10.5px; font-weight:700; text-transform:uppercase;
        letter-spacing:.07em; color:#9ca3af;
        padding:10px 16px; border-bottom:1px solid #f1f3f5;
        white-space:nowrap; background:#f9fafb;
    }
    .tbl td {
        padding:11px 16px; font-size:12.5px; color:#374151;
        border-bottom:1px solid #f8f9fa; vertical-align:middle;
    }
    .tbl tr:last-child td { border-bottom:none; }
    .tbl tr:hover td { background:#fafafa; }

    /* ── Status badge ── */
    .status-badge {
        display:inline-block; padding:3px 9px; border-radius:6px;
        font-size:10px; font-weight:700; text-transform:uppercase;
        letter-spacing:.04em; border-width:1px; border-style:solid;
    }
    .s-dibayar  { background:#f0fdf7; color:#059669; border-color:#bbf7d0; }
    .s-pending  { background:#fffbeb; color:#d97706; border-color:#fde68a; }
    .s-menunggu { background:#fffbeb; color:#d97706; border-color:#fde68a; }
    .s-ditolak  { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
    .s-default  { background:#f3f4f6; color:#6b7280; border-color:#e5e7eb; }

    /* ── Channel dot ── */
    .channel-dot { font-size:10px; font-weight:600; text-transform:uppercase; }
    .ch-onsite { color:#f97316; }
    .ch-online { color:#8b5cf6; }

    /* ── Tipe badge ── */
    .tipe-badge {
        font-size:9.5px; font-weight:700; text-transform:uppercase;
        padding:2px 7px; border-radius:99px;
    }
    .t-membership { background:#eff6ff; color:#2563eb; }
    .t-harian     { background:#f3f4f6; color:#6b7280; }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-[17px] font-bold text-gray-800 leading-tight">Riwayat Transaksi</h1>
        <p class="text-[12px] text-gray-400 mt-0.5">Seluruh histori transaksi gym Anda</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('riwayat.excel', request()->query()) }}" class="btn btn-excel">
            <i class="fa-solid fa-file-excel text-[11px]"></i> Export Excel
        </a>
        <a href="{{ route('riwayat.pdf', request()->query()) }}" class="btn btn-pdf">
            <i class="fa-solid fa-file-pdf text-[11px]"></i> Export PDF
        </a>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">

    <div class="stat-card">
        <div class="stat-icon bg-emerald-50">
            <i class="fa-solid fa-wallet text-emerald-500"></i>
        </div>
        <div class="min-w-0">
            <div class="stat-label">Total Pendapatan Terfilter</div>
            <div class="stat-value truncate">Rp {{ number_format($totalNominal,0,',','.') }}</div>
            <div class="stat-sub">Hanya transaksi berstatus <span class="font-semibold text-emerald-600">Dibayar</span></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-indigo-50">
            <i class="fa-solid fa-receipt text-indigo-500"></i>
        </div>
        <div>
            <div class="stat-label">Total Record</div>
            <div class="stat-value">{{ $data->total() }}</div>
            <div class="stat-sub">transaksi ditemukan</div>
        </div>
    </div>

</div>

{{-- ── Filter Panel ── --}}
<div class="card p-4 mb-5">
    <div class="text-[10.5px] font-bold uppercase tracking-wider text-gray-400 mb-3">Filter & Pencarian</div>
    <form action="{{ route('riwayat.index') }}" method="GET">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">

            {{-- Search ── --}}
            <div class="xl:col-span-2">
                <label class="filter-label">Cari Transaksi</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Invoice, nama tamu..."
                           class="filter-input pl-9">
                </div>
            </div>

            {{-- Dari ── --}}
            <div>
                <label class="filter-label">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
            </div>

            {{-- Sampai ── --}}
            <div>
                <label class="filter-label">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input">
            </div>

            {{-- Channel ── --}}
            <div>
                <label class="filter-label">Channel</label>
                <select name="channel" class="filter-input">
                    <option value="">Semua</option>
                    <option value="onsite" {{ request('channel')=='onsite' ? 'selected' : '' }}>Onsite</option>
                    <option value="online" {{ request('channel')=='online' ? 'selected' : '' }}>Online</option>
                </select>
            </div>

            {{-- Tipe ── --}}
            <div>
                <label class="filter-label">Tipe Paket</label>
                <select name="tipe" class="filter-input">
                    <option value="">Semua</option>
                    <option value="membership" {{ request('tipe')=='membership' ? 'selected' : '' }}>Membership</option>
                    <option value="harian"     {{ request('tipe')=='harian'     ? 'selected' : '' }}>Harian</option>
                </select>
            </div>

            {{-- Status ── --}}
            <div>
                <label class="filter-label">Status</label>
                <select name="status" class="filter-input">
                    <option value="">Semua</option>
                    <option value="dibayar" {{ request('status')=='dibayar' ? 'selected' : '' }}>Dibayar</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="ditolak" {{ request('status')=='ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            {{-- Tombol ── --}}
            <div class="flex items-end gap-2 xl:col-span-2">
                <button type="submit" class="btn btn-dark flex-1">
                    <i class="fa-solid fa-filter text-[11px]"></i> Filter
                </button>
                <a href="{{ route('riwayat.index') }}" class="btn btn-white flex-1">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
                </a>
            </div>

        </div>
    </form>
</div>

{{-- ── Table ── --}}
<div class="card">
    <div class="overflow-x-auto">
        <table class="tbl w-full">
            <thead>
                <tr>
                    <th class="text-left">Info Transaksi</th>
                    <th class="text-left">Pelanggan</th>
                    <th class="text-left">Tipe & Paket</th>
                    <th class="text-left">Pembayaran</th>
                    <th class="text-center">Status</th>
                    <th class="text-left">Bukti</th>
                    <th class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    {{-- Info Transaksi ── --}}
                    <td>
                        <div class="font-bold text-gray-800 text-[12.5px]">#{{ $item->kode_invoice }}</div>
                        <div class="text-[10.5px] text-gray-400 mt-0.5">
                            {{ $item->created_at->format('d M Y, H:i') }}
                        </div>
                    </td>

                    {{-- Pelanggan ── --}}
                    <td>
                        @if($item->member)
                            <div class="font-semibold text-gray-800 text-[12.5px]">{{ $item->member->nama }}</div>
                            <span class="tipe-badge t-membership mt-0.5 inline-block">Member</span>
                        @else
                            <div class="font-semibold text-gray-800 text-[12.5px]">{{ $item->nama_tamu }}</div>
                            <span class="tipe-badge t-harian mt-0.5 inline-block">Tamu</span>
                        @endif
                    </td>

                    {{-- Tipe & Paket ── --}}
                    <td>
                        <div class="text-[12.5px] text-gray-700">{{ $item->paket->nama_paket ?? '-' }}</div>
                        <span class="tipe-badge {{ $item->tipe==='membership'?'t-membership':'t-harian' }} mt-0.5 inline-block">
                            {{ $item->tipe }}
                        </span>
                    </td>

                    {{-- Pembayaran ── --}}
                    <td>
                        <div class="text-[12.5px] text-gray-700 capitalize">{{ $item->metode_pembayaran }}</div>
                        <span class="channel-dot {{ $item->channel==='onsite'?'ch-onsite':'ch-online' }} block mt-0.5">
                            ● {{ $item->channel }}
                        </span>
                    </td>

                    {{-- Status ── --}}
                    <td class="text-center">
                        @php
                            $sMap = [
                                'dibayar'  => 's-dibayar',
                                'menunggu' => 's-menunggu',
                                'pending'  => 's-pending',
                                'ditolak'  => 's-ditolak',
                            ];
                            $sCls = $sMap[$item->status] ?? 's-default';
                        @endphp
                        <span class="status-badge {{ $sCls }}">{{ $item->status }}</span>
                    </td>

                    {{-- Bukti ── --}}
                    <td>
                        @if($item->verifikasi && $item->verifikasi->bukti_pembayaran)
                           <a href="{{ Storage::url($item->verifikasi->bukti_pembayaran) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[11.5px] font-semibold text-blue-600 hover:underline">
                                <i class="fa-solid fa-image text-[10px]"></i> Lihat Bukti
                            </a>
                        @else
                            <span class="text-[10.5px] text-gray-400 italic">Tidak ada</span>
                        @endif
                    </td>

                    {{-- Nominal ── --}}
                    <td class="text-right">
                        <span class="font-bold text-gray-800 text-[12.5px]">
                            Rp {{ number_format($item->jumlah_bayar,0,',','.') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-14 h-14 rounded-xl bg-gray-50 flex items-center justify-center">
                                <i class="fa-solid fa-clock-rotate-left text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-[13px] text-gray-400 font-medium">Tidak ada riwayat transaksi ditemukan.</p>
                            <a href="{{ route('riwayat.index') }}" class="text-[12px] text-emerald-600 hover:underline">
                                Reset filter
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination ── --}}
    @if($data->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-[12px] text-gray-400">
            Menampilkan
            <span class="font-semibold text-gray-600">{{ $data->firstItem() ?? 0 }}–{{ $data->lastItem() ?? 0 }}</span>
            dari <span class="font-semibold text-gray-600">{{ $data->total() }}</span> record
        </p>
        {{ $data->links() }}
    </div>
    @endif
</div>

@endsection