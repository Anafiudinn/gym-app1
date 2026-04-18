@extends('layouts.admin')
@section('title', 'Manajemen Member')
@section('page-title', 'Manajemen Member')

@push('styles')
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .fade-up { animation: fadeUp 0.35s ease both; }
    .fade-up-1 { animation-delay: .05s; }
    .fade-up-2 { animation-delay: .10s; }
    .fade-up-3 { animation-delay: .15s; }
    .fade-up-4 { animation-delay: .20s; }

    .stat-card { transition: transform 0.18s ease, box-shadow 0.18s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px -4px rgba(0,0,0,0.10); }

    .member-row { transition: background 0.14s; }
    .member-row:hover .row-actions { opacity: 1; }
    .row-actions { opacity: 0; transition: opacity 0.15s; }
</style>
@endpush

@push('scripts')
<script>
async function handleToggleMember(event, memberId, currentStatus) {
    event.preventDefault();
    
    const action = currentStatus === 'aktif' ? 'nonaktifkan' : 'aktifkan';
    const newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
    
    const confirmed = await GymProAlert.confirm(
        'Ubah Status Member',
        `Apakah Anda yakin ingin ${action} member ini?`,
        `${action.charAt(0).toUpperCase() + action.slice(1)} Member`,
        'Batal'
    );
    
    if (confirmed) {
        // Update hidden input status
        event.target.closest('form').querySelector('input[name="status"]').value = newStatus;
        event.target.closest('form').submit();
    }
}
</script>
@endpush

@section('content')
<div class="space-y-5">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 fade-up fade-up-1">
        <div>
            <h1 class="text-xl font-bold text-gray-800 leading-tight">Manajemen Member</h1>
            <p class="text-xs text-gray-400 mt-0.5">Kelola seluruh data member aktif, expired, dan nonaktif</p>
        </div>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 fade-up fade-up-2">

        {{-- Total --}}
        <div class="stat-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-900 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-users text-white text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Total Member</p>
                <p class="text-3xl font-black text-gray-800 leading-none">{{ $stats['totalMembers'] }}</p>
            </div>
        </div>

        {{-- Aktif --}}
        <div class="stat-card bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 flex items-center gap-4 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent pointer-events-none"></div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-200 relative">
                <i class="fa-solid fa-circle-check text-white text-sm"></i>
            </div>
            <div class="relative">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 mb-0.5">Member Aktif</p>
                <p class="text-3xl font-black text-emerald-600 leading-none">{{ $stats['activeMembers'] }}</p>
            </div>
        </div>

        {{-- Expired --}}
        <div class="stat-card bg-white rounded-2xl border border-amber-100 shadow-sm p-5 flex items-center gap-4 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent pointer-events-none"></div>
            <div class="w-12 h-12 rounded-xl bg-amber-400 flex items-center justify-center flex-shrink-0 shadow-md shadow-amber-200 relative">
                <i class="fa-solid fa-clock text-white text-sm"></i>
            </div>
            <div class="relative">
                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-500 mb-0.5">Expired</p>
                <p class="text-3xl font-black text-amber-500 leading-none">{{ $stats['expiredMembers'] }}</p>
            </div>
        </div>

    </div>

    {{-- ===== SEARCH & FILTER ===== --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 fade-up fade-up-3">
        <form method="GET" action="{{ route('member.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-[11px]"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama atau kode member…"
                    class="w-full pl-8 pr-4 py-2 text-xs border border-gray-100 bg-gray-50 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-transparent outline-none transition placeholder-gray-300">
            </div>
            <div class="flex gap-2">
                <select name="status"
                    class="px-3 py-2 text-xs border border-gray-100 bg-gray-50 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-transparent outline-none transition text-gray-600">
                    <option value="">Semua Status</option>
                    <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                    <option value="expired"  {{ request('status') === 'expired'  ? 'selected' : '' }}>Expired</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-filter mr-1"></i>Filter
                </button>
                @if(request('search') || request('status'))
                <a href="{{ route('member.index') }}"
                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-lg text-xs font-semibold transition shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up fade-up-4">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/70">
                        <th class="text-left text-[10px] font-bold text-gray-400 px-5 py-3.5 uppercase tracking-wider">No</th>
                        <th class="text-left text-[10px] font-bold text-gray-400 px-5 py-3.5 uppercase tracking-wider">Member</th>
                        <th class="text-left text-[10px] font-bold text-gray-400 px-5 py-3.5 uppercase tracking-wider">Kode</th>
                        <th class="text-left text-[10px] font-bold text-gray-400 px-5 py-3.5 uppercase tracking-wider">Telepon</th>
                        <th class="text-left text-[10px] font-bold text-gray-400 px-5 py-3.5 uppercase tracking-wider">Paket</th>
                        <th class="text-left text-[10px] font-bold text-gray-400 px-5 py-3.5 uppercase tracking-wider whitespace-nowrap">Berlaku s/d</th>
                        <th class="text-center text-[10px] font-bold text-gray-400 px-5 py-3.5 uppercase tracking-wider">Status</th>
                        <th class="text-center text-[10px] font-bold text-gray-400 px-5 py-3.5 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($members as $member)
                    @php
                        $isExpired = $member->tanggal_kadaluarsa &&
                            \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->isPast();
                        $statusLabel = match($member->status) {
                            'aktif' => 'aktif',
                            'expired' => 'expired',
                            default => 'nonaktif'
                        };
                    @endphp
                    <tr class="member-row hover:bg-emerald-50/20 transition-colors h-16">
                        {{-- No --}}
                        <td class="px-5 py-3.5">
                            <p class="text-[13px] font-semibold text-gray-800 leading-tight">{{ $loop->iteration }}</p>
                        </td>

                        {{-- Avatar + Nama --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                {{-- Avatar gradient berdasarkan huruf --}}
                                @php
                                    $colors = ['bg-emerald-500','bg-indigo-500','bg-violet-500','bg-rose-500','bg-amber-500','bg-sky-500'];
                                    $color = $colors[ord(strtoupper($member->nama[0])) % count($colors)];
                                @endphp
                                <div class="w-9 h-9 rounded-xl {{ $color }} text-white flex items-center justify-center text-[12px] font-bold flex-shrink-0 shadow-sm">
                                    {{ strtoupper(substr($member->nama, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold text-gray-800 leading-tight truncate">{{ $member->nama }}</p>
                                    @if($member->email)
                                    <p class="text-[10px] text-gray-400 leading-tight mt-0.5 truncate">{{ $member->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Kode --}}
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-[11px] text-gray-600 bg-gray-100 border border-gray-200 px-2 py-1 rounded-lg">
                                {{ $member->kode_member }}
                            </span>
                        </td>

                        {{-- Telepon --}}
                        <td class="px-5 py-3.5 text-[12px] text-gray-500 whitespace-nowrap">
                            {{ $member->no_wa ?? '-' }}
                        </td>

                        {{-- Paket --}}
                        <td class="px-5 py-3.5">
                            @if($member->membership?->paket?->nama_paket)
                                <span class="inline-flex items-center text-[11px] font-semibold text-violet-700 bg-violet-50 border border-violet-200 px-2.5 py-1 rounded-full whitespace-nowrap">
                                    <i class="fa-solid fa-box text-[9px] mr-1.5 opacity-70"></i>
                                    {{ Str::limit($member->membership->paket->nama_paket, 15) }}
                                </span>
                            @else
                                <span class="text-[11px] text-gray-300 italic">Tanpa Paket</span>
                            @endif
                        </td>

                        {{-- Berlaku s/d --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($member->tanggal_kadaluarsa)
                                <div class="flex items-center gap-1.5">
                                    @if($isExpired)
                                        <i class="fa-solid fa-triangle-exclamation text-amber-400 text-[10px]"></i>
                                    @else
                                        <i class="fa-regular fa-calendar text-gray-300 text-[10px]"></i>
                                    @endif
                                    <span class="text-[12px] {{ $isExpired ? 'text-amber-600 font-semibold' : 'text-gray-500' }}">
                                        {{ \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->format('d M Y') }}
                                    </span>
                                </div>
                            @else
                                <span class="text-[12px] text-gray-300">-</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3.5 text-center">
                            @if($member->status === 'aktif')
                                <span class="inline-flex items-center gap-1.5 text-[10.5px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Aktif
                                </span>
                            @elseif($member->status === 'expired')
                                <span class="inline-flex items-center gap-1.5 text-[10.5px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Expired
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-[10.5px] font-bold text-gray-500 bg-gray-100 border border-gray-200 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif
                                </span>
                            @endif
                        </td>

                        {{-- Aksi - ✅ SWEETALERT2 INTEGRATED --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5 row-actions">
                                {{-- Detail --}}
                                <a href="{{ route('member.show', $member->id) }}"
                                   class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all group"
                                   title="Lihat Detail">
                                    <i class="fa-solid fa-eye text-[11px] group-hover:scale-110"></i>
                                </a>
                                
                                {{-- Toggle Status - SWEETALERT2 ✅ --}}
                                <form method="POST" action="{{ route('member.toggle', $member->id) }}"
                                      class="inline-flex">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="">
                                    <button type="submit"
                                        onclick="handleToggleMember(event, {{ $member->id }}, '{{ $statusLabel }}')"
                                        class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all group relative"
                                        title="{{ $member->status === 'aktif' ? 'Nonaktifkan Member' : 'Aktifkan Member' }}">
                                        @if($member->status === 'aktif')
                                            <i class="fa-solid fa-toggle-on text-emerald-500 text-[11px] group-hover:scale-110"></i>
                                        @else
                                            <i class="fa-solid fa-toggle-off text-gray-400 text-[11px] group-hover:scale-110"></i>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <i class="fa-solid fa-users text-4xl opacity-30"></i>
                                <p class="text-sm font-medium text-gray-400 mt-1">Belum ada data member ditemukan</p>
                                @if(request('search') || request('status'))
                                    <a href="{{ route('member.index') }}" 
                                       class="px-3 py-1.5 bg-emerald-500 text-white text-xs font-semibold rounded-lg hover:bg-emerald-600 transition mt-2 shadow-sm">
                                        <i class="fa-solid fa-rotate-left mr-1"></i>Reset Filter
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($members->hasPages())
        <div class="px-5 py-3.5 border-t border-gray-50 bg-gray-50/30 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-[11px] text-gray-400">
                Menampilkan
                <span class="font-semibold text-gray-600">{{ $members->firstItem() }}</span>–<span class="font-semibold text-gray-600">{{ $members->lastItem() }}</span>
                dari <span class="font-semibold text-gray-600">{{ $members->total() }}</span> member
            </p>
            {{ $members->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection