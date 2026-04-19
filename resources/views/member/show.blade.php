@extends('layouts.admin')
@section('title', 'Detail Member')
@section('page-title', 'Detail Member')

@section('content')

<div class="mb-4">
    <a href="{{ route('member.index') }}" class="inline-flex items-center gap-2 text-[13px] text-gray-500 hover:text-gray-700 transition">
        <i class="fa-solid fa-chevron-left text-[11px]"></i> Kembali
    </a>
</div>

{{-- Grid Utama: 1 Kolom di HP, 3 Kolom di Laptop --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- LEFT COL --}}
    <div class="space-y-4">
        {{-- MEMBERSHIP CARD (Info Lengkap) --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg shadow-emerald-200 relative overflow-hidden">
            {{-- Watermark Icon --}}
            <i class="fa-solid fa-id-card absolute -right-4 -bottom-4 text-white/10 text-8xl rotate-12"></i>

            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="text-[10px] font-bold tracking-[0.2em] opacity-80 uppercase">GymPro Membership</div>
                <span class="text-[11px] font-semibold bg-white/20 px-2.5 py-1 rounded-full backdrop-blur-sm">
                    {{ strtoupper($member->status) }}
                </span>
            </div>

            <div class="mb-6 relative z-10">
                <div class="text-2xl font-bold leading-tight">{{ $member->nama }}</div>
                <div class="text-[13px] opacity-80 font-mono tracking-wider">{{ $member->kode_member }}</div>
            </div>

            <div class="grid grid-cols-2 gap-y-4 relative z-10">
                <div>
                    <div class="text-[10px] opacity-60 uppercase tracking-wide mb-0.5">Paket</div>
                    <div class="text-[13px] font-semibold">{{ $member->membership?->paket?->nama_paket ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[10px] opacity-60 uppercase tracking-wide mb-0.5">WhatsApp</div>
                    <div class="text-[13px] font-semibold">{{ $member->no_wa ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[10px] opacity-60 uppercase tracking-wide mb-0.5">Berlaku s/d</div>
                    <div class="text-[13px] font-semibold">
                        {{ $member->tanggal_kadaluarsa ? \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->format('d M Y') : '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-[10px] opacity-60 uppercase tracking-wide mb-0.5">Gender</div>
                    <div class="text-[13px] font-semibold">{{ ucfirst($member->jenis_kelamin ?? '-') }}</div>
                </div>
            </div>
        </div>

        {{-- QUICK ACTIONS --}}
        <div class="grid grid-cols-1 gap-2">
            <button onclick="document.getElementById('modal-edit-member').classList.remove('hidden')"
                class="w-full py-2.5 text-[13px] bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit Profil Member
            </button>

            {{-- TOMBOL BARU: Perpanjang --}}
            <a href="{{ route('transaksi.index') }}"
                class="w-full py-2.5 text-[13px] bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition flex items-center justify-center gap-2 text-center">
                <i class="fa-solid fa-rotate text-[11px]"></i> Perpanjang Membership
</a>
        </div>
    </div>

    {{-- RIGHT COL --}}
  <div class="lg:col-span-2 space-y-4">
    {{-- RIWAYAT TRANSAKSI (Limit 5) --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[13px] font-semibold text-gray-700">5 Transaksi Terakhir</h3>
            <i class="fa-solid fa-clock-rotate-left text-gray-300"></i>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($transaksi->take(5) as $trx)
            <div class="flex items-center justify-between py-3">
                <div>
                    {{-- Badge Tipe agar lebih rapi --}}
                    <div class="text-[13px] font-medium text-gray-800">{{ ucfirst($trx->tipe) }}</div>
                    <div class="text-[11px] text-gray-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($trx->tanggal_pembayaran ?? $trx->created_at)->format('d M Y') }} · {{ $trx->paket->nama_paket ?? '-' }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-[13px] font-semibold text-gray-800">Rp {{ number_format($trx->jumlah_bayar) }}</div>
                    
                    {{-- LOGIKA STATUS DINAMIS --}}
                    @if($trx->status === 'dibayar')
                        <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">LUNAS</span>
                    @elseif($trx->status === 'batal')
                        <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">DIBATALKAN</span>
                    @elseif($trx->status === 'pending')
                        <span class="text-[10px] text-amber-500 font-bold uppercase tracking-wider">PENDING</span>
                    @else
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ strtoupper($trx->status) }}</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-gray-400 text-[13px]">Belum ada transaksi</div>
            @endforelse
        </div>
    </div>


        {{-- RIWAYAT ABSENSI (Limit 5) --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[13px] font-semibold text-gray-700">5 Absensi Terakhir</h3>
                <i class="fa-solid fa-fingerprint text-gray-300"></i>
            </div>
            <div class="divide-y divide-gray-50">
                {{-- Gunakan variabel $absensi yang baru dikirim dari controller --}}
                @forelse($absensi as $absen)
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-day text-gray-400 text-[12px]"></i>
                        </div>
                        <div>
                            <div class="text-[13px] text-gray-700 font-medium">
                                {{-- Gunakan created_at atau kolom tanggal di tabel absensimu --}}
                                {{ \Carbon\Carbon::parse($absen->created_at)->translatedFormat('d M Y') }}
                            </div>
                            <div class="text-[11px] text-gray-400">Hadir pada sesi latihan</div>
                        </div>
                    </div>
                    <span class="text-[12px] font-mono text-emerald-600 bg-emerald-50 px-2 py-1 rounded">
                        {{ \Carbon\Carbon::parse($absen->created_at)->format('H:i') }}
                    </span>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-[13px]">Belum ada riwayat absensi</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL EDIT PROFILE --}}
    <div id="modal-edit-member" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h2 class="font-bold text-gray-800 text-[15px]">Edit Profil</h2>
                <button onclick="document.getElementById('modal-edit-member').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('member.update', $member->id) }}" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[12px] font-semibold text-gray-600 mb-1">NAMA LENGKAP</label>
                    <input type="text" name="nama" value="{{ $member->nama }}" required
                        class="w-full px-3 py-2 text-[13px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-gray-600 mb-1">NO. WHATSAPP</label>
                    <input type="text" name="no_wa" value="{{ $member->no_wa }}" required
                        class="w-full px-3 py-2 text-[13px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-member').classList.add('hidden')"
                        class="flex-1 py-2 text-[13px] font-semibold text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200 transition">BATAL</button>
                    <button type="submit"
                        class="flex-1 py-2 text-[13px] font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 shadow-md shadow-blue-100 transition">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
    @endsection