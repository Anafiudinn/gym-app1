@extends('layouts.admin')
@section('title', 'Manajemen Member')
@section('page-title', 'Manajemen Member')

@section('content')


{{-- SEARCH & FILTER --}}
<div class="mb-4">
    <form method="GET" action="{{ route('member.index') }}" class="flex flex-col md:flex-row gap-2">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama atau kode member…"
                class="w-full pl-9 pr-4 py-2 text-[13px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-400 outline-none bg-white">
        </div>
        <div class="flex gap-2">
            <select name="status" class="flex-1 md:w-40 px-3 py-2 text-[13px] border border-gray-200 rounded-lg bg-white">
                <option value="">Semua Status</option>
                <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                <option value="expired"  {{ request('status') === 'expired'  ? 'selected' : '' }}>Expired</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-[13px] font-medium transition">Cari</button>
        </div>
    </form>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px]">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/70">
                    <th class="text-left text-[10.5px] font-semibold text-gray-400 px-4 py-3 uppercase tracking-wider">#</th>
                    <th class="text-left text-[10.5px] font-semibold text-gray-400 px-4 py-3 uppercase tracking-wider">Member</th>
                    <th class="text-left text-[10.5px] font-semibold text-gray-400 px-4 py-3 uppercase tracking-wider">Kode</th>
                    <th class="text-left text-[10.5px] font-semibold text-gray-400 px-4 py-3 uppercase tracking-wider">Telepon</th>
                    <th class="text-left text-[10.5px] font-semibold text-gray-400 px-4 py-3 uppercase tracking-wider whitespace-nowrap">Berlaku s/d</th>
                    <th class="text-center text-[10.5px] font-semibold text-gray-400 px-4 py-3 uppercase tracking-wider">Status</th>
                    <th class="text-center text-[10.5px] font-semibold text-gray-400 px-4 py-3 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($members as $member)
                @php
                    $isExpiredSoon = $member->tanggal_kadaluarsa &&
                        \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->isPast();
                @endphp
                <tr class="hover:bg-gray-50/60 transition">
                    {{-- No. --}}
                    <td class="px-4 py-3 text-[13px] text-gray-500 whitespace-nowrap">
                        {{ $members->firstItem() + $loop->index }}
                    </td>

                    {{-- Nama + Avatar --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-[11px] font-semibold flex-shrink-0 border border-emerald-100">
                                {{ strtoupper(substr($member->nama, 0, 2)) }}
                            </div>
                            <span class="text-[13px] font-medium text-gray-800 whitespace-nowrap">{{ $member->nama }}</span>
                        </div>
                    </td>

                    {{-- Kode --}}
                    <td class="px-4 py-3">
                        <span class="font-mono text-[11.5px] text-gray-500 bg-gray-100 px-2 py-1 rounded-md border border-gray-200">
                            {{ $member->kode_member }}
                        </span>
                    </td>

                    {{-- Telepon --}}
                    <td class="px-4 py-3 text-[13px] text-gray-500 whitespace-nowrap">{{ $member->no_wa ?? '-' }}</td>

                    {{-- Berlaku s/d --}}
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($member->tanggal_kadaluarsa)
                            <span class="text-[12.5px] {{ $isExpiredSoon ? 'text-amber-600 font-medium' : 'text-gray-500' }}">
                                {{ \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->format('d M Y') }}
                            </span>
                        @else
                            <span class="text-[12.5px] text-gray-400">-</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3 text-center">
                        @if($member->status === 'aktif')
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif
                        </span>
                        @elseif($member->status === 'expired')
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Expired
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif
                        </span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('member.show', $member->id) }}"
                                class="w-8 h-8 flex items-center justify-center text-gray-800 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Detail">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <form method="POST" action="{{ route('member.toggle', $member->id) }}" onsubmit="return confirm('Ubah status member ini?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center text-dark hover:text-emerald-500 hover:bg-emerald-50 rounded-lg transition"
                                    title="{{ $member->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-16 text-center text-gray-400">
                        <div class="flex flex-col items-center">
                            <svg class="w-10 h-10 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <p class="text-sm">Belum ada data member ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($members->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between gap-4">
        <p class="text-[12px] text-gray-400">
            Menampilkan {{ $members->firstItem() }}–{{ $members->lastItem() }} dari {{ $members->total() }} member
        </p>
        {{ $members->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection