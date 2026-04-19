@extends('layouts.admin')

@section('title', 'Manajemen Member')
@section('page-title', 'Manajemen Member')

@push('styles')
<style>
    /* ── Card base ── */
    .card { background:#fff; border-radius:12px; border:1px solid #e9ecf0; }

    /* ── Badge status ── */
    .badge-status {
        display:inline-flex; align-items:center; gap:5px;
        padding:3px 10px; border-radius:99px;
        font-size:11px; font-weight:600;
    }
    .badge-aktif    { background:#f0fdf7; color:#059669; }
    .badge-expired  { background:#fff7ed; color:#d97706; }
    .badge-nonaktif { background:#f3f4f6; color:#6b7280; }
    .badge-dot      { width:6px; height:6px; border-radius:50%; flex-shrink:0; }

    /* ── Stat cards ── */
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
    .stat-value { font-size:22px; font-weight:700; color:#111827; line-height:1.1; }

    /* ── Table ── */
    .tbl th {
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.07em; color:#9ca3af;
        padding:10px 14px; border-bottom:1px solid #f1f3f5;
        white-space:nowrap;
    }
    .tbl td {
        padding:11px 14px; font-size:13px; color:#374151;
        border-bottom:1px solid #f8f9fa; vertical-align:middle;
    }
    .tbl tr:last-child td { border-bottom:none; }
    .tbl tr:hover td { background:#fafafa; }

    /* ── Avatar ── */
    .member-avatar {
        width:32px; height:32px; border-radius:9px;
        background:#f0fdf7; color:#059669;
        display:flex; align-items:center; justify-content:center;
        font-size:11px; font-weight:700; flex-shrink:0;
        border:1px solid #bbf7d0;
    }

    /* ── Action button ── */
    .act-btn {
        width:30px; height:30px;
        display:flex; align-items:center; justify-content:center;
        border-radius:8px; border:none; cursor:pointer;
        transition:background .15s, color .15s;
        color:#6b7280; background:transparent;
    }
    .act-btn:hover { color:#2563eb; background:#eff6ff; }
    .act-btn.danger:hover { color:#dc2626; background:#fef2f2; }
    .act-btn.success:hover { color:#059669; background:#f0fdf7; }

    /* ── Filter form ── */
    .filter-input {
        padding:7px 12px; border:1px solid #e5e7eb; border-radius:8px;
        font-size:12.5px; color:#374151; background:#fff; outline:none;
        transition:border .15s, box-shadow .15s;
    }
    .filter-input:focus { border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.1); }

    /* ── Pagination style override ── */
    .pagination { display:flex; gap:4px; flex-wrap:wrap; }
    .pagination .page-link {
        display:flex; align-items:center; justify-content:center;
        min-width:30px; height:30px; padding:0 8px;
        border-radius:7px; font-size:12px; font-weight:500;
        color:#374151; background:#fff; border:1px solid #e5e7eb;
        transition:all .15s; text-decoration:none;
    }
    .pagination .page-link:hover { background:#f0fdf7; border-color:#10b981; color:#059669; }
    .pagination .active .page-link { background:#10b981; border-color:#10b981; color:#fff; }
    .pagination .disabled .page-link { color:#d1d5db; pointer-events:none; }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-[17px] font-bold text-gray-800 leading-tight">Manajemen Member</h1>
        <p class="text-[12px] text-gray-400 mt-0.5">Daftar seluruh member gym Anda</p>
    </div>
    {{-- Contoh: jika ada route tambah member --}}
    {{-- <a href="{{ route('member.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[12.5px] font-semibold rounded-lg transition">
        <i class="fa-solid fa-plus text-[11px]"></i> Tambah Member
    </a> --}}
</div>

{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    <div class="stat-card">
        <div class="stat-icon bg-emerald-50">
            <i class="fa-solid fa-users text-emerald-500"></i>
        </div>
        <div>
            <div class="stat-label">Total Member</div>
            <div class="stat-value">{{ $members->total() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-blue-50">
            <i class="fa-solid fa-circle-check text-blue-500"></i>
        </div>
        <div>
            <div class="stat-label">Aktif</div>
            <div class="stat-value">{{ $members->getCollection()->where('status','aktif')->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-orange-50">
            <i class="fa-solid fa-clock text-orange-400"></i>
        </div>
        <div>
            <div class="stat-label">Expired</div>
            <div class="stat-value">{{ $members->getCollection()->where('status','expired')->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-gray-50">
            <i class="fa-solid fa-user-slash text-gray-400"></i>
        </div>
        <div>
            <div class="stat-label">Nonaktif</div>
            <div class="stat-value">{{ $members->getCollection()->where('status','nonaktif')->count() }}</div>
        </div>
    </div>
</div>

{{-- ── Table Card ── --}}
<div class="card">

    {{-- Filter & Search bar ── --}}
    <div class="p-4 border-b border-gray-100">
        <form method="GET" action="{{ route('member.index') }}"
              class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau kode member…"
                       class="filter-input w-full pl-9">
            </div>
            <select name="status" class="filter-input sm:w-40">
                <option value="">Semua Status</option>
                <option value="aktif"    {{ request('status')==='aktif'    ? 'selected' : '' }}>Aktif</option>
                <option value="expired"  {{ request('status')==='expired'  ? 'selected' : '' }}>Expired</option>
                <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[12.5px] font-semibold rounded-lg transition">
                <i class="fa-solid fa-filter text-[11px]"></i> Filter
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('member.index') }}"
               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-[12.5px] font-semibold rounded-lg transition">
                <i class="fa-solid fa-xmark text-[11px]"></i> Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Table ── --}}
    <div class="overflow-x-auto">
        <table class="tbl w-full">
            <thead>
                <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">Member</th>
                    <th class="text-left">Kode</th>
                    <th class="text-left">Telepon</th>
                    <th class="text-left">Berlaku s/d</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                @php
                    $expired = $member->tanggal_kadaluarsa &&
                        \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->isPast();
                @endphp
                <tr>
                    <td class="text-gray-400 text-[12px]">{{ $members->firstItem() + $loop->index }}</td>

                    {{-- Nama ── --}}
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="member-avatar">
                                {{ strtoupper(substr($member->nama,0,2)) }}
                            </div>
                            <span class="font-semibold text-gray-800 text-[13px]">{{ $member->nama }}</span>
                        </div>
                    </td>

                    {{-- Kode ── --}}
                    <td>
                        <span class="font-mono text-[11.5px] text-gray-500 bg-gray-100 px-2 py-1 rounded-md border border-gray-200">
                            {{ $member->kode_member }}
                        </span>
                    </td>

                    {{-- Telepon ── --}}
                    <td class="text-gray-500 text-[12.5px] whitespace-nowrap">{{ $member->no_wa ?? '-' }}</td>

                    {{-- Berlaku s/d ── --}}
                    <td class="whitespace-nowrap">
                        @if($member->tanggal_kadaluarsa)
                            <span class="text-[12.5px] {{ $expired ? 'text-amber-600 font-semibold' : 'text-gray-500' }}">
                                {{ \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->format('d M Y') }}
                            </span>
                        @else
                            <span class="text-[12.5px] text-gray-400">–</span>
                        @endif
                    </td>

                    {{-- Status ── --}}
                    <td class="text-center">
                        @if($member->status === 'aktif')
                            <span class="badge-status badge-aktif">
                                <span class="badge-dot bg-emerald-500"></span>Aktif
                            </span>
                        @elseif($member->status === 'expired')
                            <span class="badge-status badge-expired">
                                <span class="badge-dot bg-amber-500"></span>Expired
                            </span>
                        @else
                            <span class="badge-status badge-nonaktif">
                                <span class="badge-dot bg-gray-400"></span>Nonaktif
                            </span>
                        @endif
                    </td>

                    {{-- Aksi ── --}}
                    <td>
                        <div class="flex items-center justify-center gap-1">
                            {{-- Detail ── --}}
                            <a href="{{ route('member.show', $member->id) }}"
                               class="act-btn" title="Lihat Detail">
                                <i class="fa-solid fa-eye text-[11px]"></i>
                            </a>

                            {{-- Toggle Status ── --}}
                            <form method="POST" action="{{ route('member.toggle', $member->id) }}"
                                  onsubmit="return confirm('Ubah status member ini?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="act-btn success"
                                        title="{{ $member->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fa-solid {{ $member->status === 'aktif' ? 'fa-toggle-on' : 'fa-toggle-off' }} text-[13px]"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-14 h-14 rounded-xl bg-gray-50 flex items-center justify-center">
                                <i class="fa-solid fa-users text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-[13px] text-gray-400 font-medium">Belum ada data member ditemukan</p>
                            @if(request('search') || request('status'))
                                <a href="{{ route('member.index') }}"
                                   class="text-[12px] text-emerald-600 hover:underline mt-1">Reset filter</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination ── --}}
    @if($members->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-[12px] text-gray-400">
            Menampilkan <span class="font-semibold text-gray-600">{{ $members->firstItem() }}–{{ $members->lastItem() }}</span>
            dari <span class="font-semibold text-gray-600">{{ $members->total() }}</span> member
        </p>
        {{ $members->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection