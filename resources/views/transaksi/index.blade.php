@extends('layouts.admin')
@section('title', 'Transaksi')
@section('page-title', 'Transaksi')

@push('styles')
<style>
    .tab-btn {
        padding: 10px 4px;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 2px solid transparent;
        border-top: none;
        border-left: none;
        border-right: none;
        background: none;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }

    .tab-btn:hover {
        color: #374151;
    }

    .tab-btn.active {
        color: #10b981;
        border-bottom-color: #10b981;
    }

    .form-input {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 9px 13px;
        font-size: 13px;
        color: #111827;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
    }

    .form-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .form-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #9ca3af;
        margin-bottom: 5px;
    }

    .btn-green {
        width: 100%;
        background: #10b981;
        color: #fff;
        font-size: 12.5px;
        font-weight: 700;
        padding: 11px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
    }

    .btn-green:hover {
        background: #059669;
    }

    .btn-green:active {
        transform: scale(0.985);
    }

    .btn-outline-green {
        width: 100%;
        background: #f0fdf4;
        color: #059669;
        font-size: 12.5px;
        font-weight: 700;
        padding: 11px;
        border-radius: 10px;
        border: 1px solid #bbf7d0;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-outline-green:hover {
        background: #dcfce7;
    }

    .member-search-wrapper {
        position: relative;
    }

    .member-search-input {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 9px 13px 9px 36px;
        font-size: 13px;
        color: #111827;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
    }

    .member-search-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .member-search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 12px;
        pointer-events: none;
    }

    .member-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        z-index: 50;
        max-height: 200px;
        overflow-y: auto;
        display: none;
    }

    .member-dropdown.show {
        display: block;
    }

    .member-option {
        padding: 9px 13px;
        font-size: 12.5px;
        cursor: pointer;
        transition: background 0.1s;
        border-bottom: 1px solid #f3f4f6;
    }

    .member-option:last-child {
        border-bottom: none;
    }

    .member-option:hover {
        background: #f0fdf4;
    }

    .member-option .member-name {
        font-weight: 600;
        color: #111827;
    }

    .member-option .member-code {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 1px;
    }

    .member-option .member-status {
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
        float: right;
        margin-top: 2px;
    }

    .member-option .member-status.aktif {
        background: #d1fae5;
        color: #065f46;
    }

    .member-option .member-status.expired {
        background: #fee2e2;
        color: #991b1b;
    }

    .member-selected-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 10px 13px;
        margin-top: 8px;
        display: none;
    }

    .member-selected-card.show {
        display: block;
    }

    .member-selected-card .ms-name {
        font-size: 13px;
        font-weight: 700;
        color: #065f46;
    }

    .member-selected-card .ms-meta {
        font-size: 11px;
        color: #34d399;
        margin-top: 2px;
    }

    .no-member-found {
        padding: 12px 13px;
        font-size: 12px;
        color: #9ca3af;
        text-align: center;
    }
</style>
@endpush

@section('content')

{{-- Cek Error Validasi (Nomor Duplikat, dll) --}}
@if($errors->any())
    <div style="background: rgba(255, 68, 68, 0.1); border: 1.5px solid #ff4444; color: #ff4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.8rem;">
        <span style="font-size: 1.5rem;">⚠️</span>
        <div style="font-size: 0.9rem;">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

{{-- Cek Error Sistem (dari session error) --}}
@if(session('error'))
    <div style="background: rgba(255, 165, 0, 0.1); border: 1.5px solid #ffa500; color: #ffa500; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    {{-- KIRI (4 kolom) --}}
    <div class="lg:col-span-4 space-y-4 order-1">

        {{-- Summary --}}
        <div class="bg-white rounded-xl border border-gray-100 px-5 py-4">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Pemasukan Hari Ini</p>
            <p class="text-[22px] font-bold text-gray-800 leading-none">Rp{{ number_format($totalHariIni, 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">{{ $countHariIni }} transaksi</p>
        </div>
        {{-- Form dengan 3 Tab --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="flex items-center border-b border-gray-100 w-full">

                <button type="button" onclick="switchTab('tamu')" id="btn-tamu" class="tab-btn active flex-1 py-3 text-center">
                    <i class="fa-solid fa-person-walking text-[10px] mr-1"></i>Tamu
                </button>

                <button type="button" onclick="switchTab('member-baru')" id="btn-member-baru" class="tab-btn flex-1 py-3 text-center border-x border-gray-50">
                    <i class="fa-solid fa-user-plus text-[10px] mr-1"></i>Member Baru
                </button>

                <button type="button" onclick="switchTab('perpanjang')" id="btn-perpanjang" class="tab-btn flex-1 py-3 text-center">
                    <i class="fa-solid fa-rotate-right text-[10px] mr-1"></i>Perpanjang
                </button>

            </div>
            <div class="p-5">

                {{-- TAB 1: TAMU HARIAN --}}
                <div id="pane-tamu">
                    <div class="flex items-center justify-between bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3 mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Paket Default</p>
                            <p class="text-[14px] font-bold text-emerald-900 mt-0.5">
                                {{ $paketDefault->nama_paket ?? 'Harian' }}
                            </p>
                        </div>
                        <span class="text-[20px] font-black text-emerald-600">
                            Rp{{ number_format($paketDefault->harga ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('transaksi.harian') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="form-label">Nama Pengunjung</label>
                            <input type="text" name="nama_tamu" placeholder="Masukkan nama tamu..." class="form-input" required>
                        </div>
                     <div>
    <label class="form-label">Metode Pembayaran</label>
    <div class="grid grid-cols-2 gap-3 mt-2">
        {{-- Opsi Cash --}}
        <label class="cursor-pointer group">
            <input type="radio" name="metode_pembayaran" value="cash" class="hidden peer" required checked>
            <div class="flex items-center justify-center py-3 px-4 rounded-xl border-2 border-slate-200 bg-white text-slate-600 transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 hover:bg-slate-50">
                <i class="fa-solid fa-money-bill-wave mr-2"></i>
                <span class="font-bold text-sm">CASH</span>
            </div>
        </label>

        {{-- Opsi Transfer --}}
        <label class="cursor-pointer group">
            <input type="radio" name="metode_pembayaran" value="transfer" class="hidden peer">
            <div class="flex items-center justify-center py-3 px-4 rounded-xl border-2 border-slate-200 bg-white text-slate-600 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 hover:bg-slate-50">
                <i class="fa-solid fa-mobile-screen mr-2"></i>
                <span class="font-bold text-sm">TRANSFER</span>
            </div>
        </label>
    </div>
</div>
                      
                        <button type="submit" class="btn-green">
                            <i class="fa-solid fa-cash-register mr-1.5"></i> Bayar Sekarang
                        </button>
                    </form>
                </div>

                {{-- TAB 2: MEMBER BARU --}}
                <div id="pane-member-baru" class="hidden">
                    <form method="POST" action="/transaksi/membership" class="space-y-3">
                        @csrf
                        <input type="hidden" name="tipe_member" value="baru">

                        <div class="space-y-2.5 p-3.5 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <div>
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" placeholder="Nama lengkap member baru" class="form-input" required>
                            </div>
                          {{-- Ganti bagian input no_wa kamu dengan ini --}}
<div>
    <label class="form-label">No. WhatsApp</label>
    <input type="tel" 
           name="no_wa" 
           placeholder="Contoh: 08123456789" 
           class="form-input"
           {{-- Script ini memaksa hanya angka yang bisa diketik --}}
           oninput="this.value = this.value.replace(/[^0-9]/g, '');"
           {{-- required_if di controller, tapi di view kita kasih required agar admin tidak lupa --}}
           required>
    <p class="text-[10px] text-gray-400 mt-1">Gunakan format angka saja (08...)</p>
</div>
                            <div>
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-input">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Paket</label>
                            <select name="paket_id" class="form-input" required>
                                <option value="">— Pilih Durasi Paket —</option>
                                @foreach($paket as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_paket }} — Rp{{ number_format($p->harga, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div>
    <label class="form-label">Metode Pembayaran</label>
    <div class="grid grid-cols-2 gap-3 mt-2">
        {{-- Opsi Cash --}}
        <label class="cursor-pointer group">
            <input type="radio" name="metode_pembayaran" value="cash" class="hidden peer" required checked>
            <div class="flex items-center justify-center py-3 px-4 rounded-xl border-2 border-slate-200 bg-white text-slate-600 transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 hover:bg-slate-50">
                <i class="fa-solid fa-money-bill-wave mr-2"></i>
                <span class="font-bold text-sm">CASH</span>
            </div>
        </label>

        {{-- Opsi Transfer --}}
        <label class="cursor-pointer group">
            <input type="radio" name="metode_pembayaran" value="transfer" class="hidden peer">
            <div class="flex items-center justify-center py-3 px-4 rounded-xl border-2 border-slate-200 bg-white text-slate-600 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 hover:bg-slate-50">
                <i class="fa-solid fa-mobile-screen mr-2"></i>
                <span class="font-bold text-sm">TRANSFER</span>
            </div>
        </label>
    </div>
</div>

                       <button type="submit" class="btn-outline-green w-full py-3" onclick="this.disabled=true;this.form.submit();">
    <i class="fa-solid fa-id-card mr-1.5"></i> Daftarkan Member
</button>
                    </form>
                </div>

                {{-- TAB 3: PERPANJANG --}}
                <div id="pane-perpanjang" class="hidden">
                    <form method="POST" action="/transaksi/membership" class="space-y-3">
                        @csrf
                        <input type="hidden" name="tipe_member" value="perpanjang">

                        <div>
                            <label class="form-label">Cari Member</label>
                            <input type="hidden" name="member_id" id="member_id_hidden">

                            <div class="member-search-wrapper">
                                <i class="fa-solid fa-magnifying-glass member-search-icon"></i>
                                <input
                                    type="text"
                                    id="member_search_input"
                                    class="member-search-input"
                                    placeholder="Ketik nama atau kode member..."
                                    autocomplete="off">
                                <div class="member-dropdown" id="member_dropdown"></div>
                            </div>

                            <div class="member-selected-card" id="member_selected_card">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="ms-name" id="ms-name"></div>
                                        <div class="ms-meta" id="ms-meta"></div>
                                    </div>
                                    <button type="button" onclick="clearMember()"
                                        class="text-[10px] text-red-400 hover:text-red-600 font-semibold ml-2 flex-shrink-0">
                                        <i class="fa-solid fa-xmark"></i> Ganti
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Paket</label>
                            <select name="paket_id" class="form-input" required>
                                <option value="">— Pilih Durasi Paket —</option>
                                @foreach($paket as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_paket }} — Rp{{ number_format($p->harga, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>

                     <div>
    <label class="form-label">Metode Pembayaran</label>
    <div class="grid grid-cols-2 gap-3 mt-2">
        {{-- Opsi Cash --}}
        <label class="cursor-pointer group">
            <input type="radio" name="metode_pembayaran" value="cash" class="hidden peer" required checked>
            <div class="flex items-center justify-center py-3 px-4 rounded-xl border-2 border-slate-200 bg-white text-slate-600 transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 hover:bg-slate-50">
                <i class="fa-solid fa-money-bill-wave mr-2"></i>
                <span class="font-bold text-sm">CASH</span>
            </div>
        </label>

        {{-- Opsi Transfer --}}
        <label class="cursor-pointer group">
            <input type="radio" name="metode_pembayaran" value="transfer" class="hidden peer">
            <div class="flex items-center justify-center py-3 px-4 rounded-xl border-2 border-slate-200 bg-white text-slate-600 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 hover:bg-slate-50">
                <i class="fa-solid fa-mobile-screen mr-2"></i>
                <span class="font-bold text-sm">TRANSFER</span>
            </div>
        </label>
    </div>
</div>

                        <button type="submit" class="btn-outline-green">
                            <i class="fa-solid fa-rotate-right mr-1.5"></i> Perpanjang Membership
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- KANAN (8 kolom) --}}
    <div class="lg:col-span-8 space-y-3 order-2">

        {{-- Filter Bar --}}
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <form action="{{ route('transaksi.index') }}" method="GET">
                @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}"> @endif
                <div class="flex flex-wrap gap-2.5">
                    <div class="flex-1 min-w-[160px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama / invoice..."
                            class="form-input text-[12px]" style="padding: 8px 12px;">
                    </div>
                    <div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="form-input text-[12px]" style="padding: 8px 12px; width: 138px;">
                    </div>
                    <div>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="form-input text-[12px]" style="padding: 8px 12px; width: 138px;">
                    </div>
                    <div>
                        <select name="tipe" class="form-input text-[12px]" style="padding: 8px 12px; width: 130px;">
                            <option value="">Semua Tipe</option>
                            <option value="harian" {{ request('tipe') === 'harian' ? 'selected' : '' }}>Harian</option>
                            <option value="membership" {{ request('tipe') === 'membership' ? 'selected' : '' }}>Membership</option>
                        </select>
                    </div>
                    <div>
                        <select name="status" class="form-input text-[12px]" style="padding: 8px 12px; width: 130px;">
                            <option value="">Semua Status</option>
                            <option value="dibayar" {{ request('status') === 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold px-4 py-2 rounded-lg transition">
                        <i class="fa-solid fa-filter text-[10px] mr-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search','date_from','date_to','tipe','status']))
                    <a href="{{ route('transaksi.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-500 text-[12px] font-semibold px-4 py-2 rounded-lg transition flex items-center">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                <div>
                    <h3 class="text-[12.5px] font-bold text-gray-700">Riwayat Transaksi Onsite</h3>
                    <p class="text-[11px] text-emerald-500 mt-0.5 font-medium">
                        <i class="fa-solid fa-calendar-day mr-1"></i> Hari Ini: {{ now()->format('d M Y') }}
                    </p>
                </div>
                <span class="flex items-center gap-1.5 text-[10.5px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    Live
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pembayaran</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Paket</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($data as $d)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-4 py-3.5 text-[12px] text-gray-400">
                                {{ $data->firstItem() + $loop->index }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-semibold text-gray-800 group-hover:text-emerald-600 transition">
                                    {{ $d->kode_invoice }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-[12.5px] font-semibold text-gray-700">{{ $d->created_at->format('d M Y') }}</div>
                                <div class="text-[10.5px] text-gray-400">{{ $d->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-[12.5px] font-semibold text-gray-800">
                                    {{ $d->member->nama ?? $d->nama_tamu }}
                                </div>
                                <div>
                                    <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide
                                    {{ $d->tipe === 'membership' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $d->tipe }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide
                                    {{ $d->metode_pembayaran === 'transfer' ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600' }}">
                                    {{ $d->metode_pembayaran ?? 'cash' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-[12.5px] text-gray-600">
                                {{ $d->paket->nama_paket ?? 'Visit Harian' }}
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="text-[13px] font-bold text-gray-900">
                                    Rp{{ number_format($d->jumlah_bayar, 0, ',', '.') }}
                                </div>
                                <span class="text-[9.5px] font-semibold uppercase
                                    {{ $d->status === 'dibayar' ? 'text-emerald-500' : ($d->status === 'pending' ? 'text-orange-400' : 'text-red-400') }}">
                                    {{ $d->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <i class="fa-solid fa-receipt text-3xl text-gray-200 mb-3 block"></i>
                                <p class="text-[12px] text-gray-400">Tidak ada transaksi ditemukan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->hasPages())
            <div class="px-5 py-3.5 border-t border-gray-100 flex items-center justify-between">
                <p class="text-[11.5px] text-gray-400">
                    Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }} dari {{ $data->total() }} transaksi
                </p>
                {{ $data->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    (function() {

@php
    // Pastikan tidak ada spasi di antara - dan >
    $membersJson = $members->map(function($m) {
        return [
            'id' => $m->id,
            'nama' => $m->nama,
            'kode_member' => $m->kode_member,
            'no_wa' => $m->no_wa ?? '',
            'tanggal_kadaluarsa' => $m->tanggal_kadaluarsa,
            'status' => $m->status,
        ];
    });
@endphp

        var allMembers = @json($membersJson);
        var defaultTab = @js($activeTab ?? 'tamu');
        var preSelectedId = @js($selectedMemberId ?? null);

        var TABS = ['tamu', 'member-baru', 'perpanjang'];

        function switchTab(tab) {
            TABS.forEach(function(t) {
                var pane = document.getElementById('pane-' + t);
                var btn = document.getElementById('btn-' + t);
                if (pane) pane.classList.toggle('hidden', t !== tab);
                if (btn) btn.classList.toggle('active', t === tab);
            });
        }
        window.switchTab = switchTab;

        var searchInput = document.getElementById('member_search_input');
        var dropdown = document.getElementById('member_dropdown');
        var hiddenInput = document.getElementById('member_id_hidden');
        var selectedCard = document.getElementById('member_selected_card');
        var msName = document.getElementById('ms-name');
        var msMeta = document.getElementById('ms-meta');

        function formatTgl(tgl) {
            if (!tgl) return 'Belum ada data';
            var d = new Date(tgl);
            var bln = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return d.getDate() + ' ' + bln[d.getMonth()] + ' ' + d.getFullYear();
        }

        function renderDropdown(results) {
            dropdown.innerHTML = '';
            if (!results.length) {
                dropdown.innerHTML = '<div class="no-member-found"><i class="fa-solid fa-user-slash mr-1"></i> Member tidak ditemukan</div>';
            } else {
                results.forEach(function(m) {
                    var expired = m.tanggal_kadaluarsa && new Date(m.tanggal_kadaluarsa) < new Date();
                    var statusLabel = expired ? 'expired' : m.status;
                    var statusClass = (m.status === 'aktif' && !expired) ? 'aktif' : 'expired';
                    var el = document.createElement('div');
                    el.className = 'member-option';
                    el.innerHTML =
                        '<span class="member-status ' + statusClass + '">' + statusLabel + '</span>' +
                        '<div class="member-name">' + m.nama + '</div>' +
                        '<div class="member-code">' + m.kode_member + (m.no_wa ? ' \u00b7 ' + m.no_wa : '') + '</div>';
                    el.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        selectMember(m);
                    });
                    dropdown.appendChild(el);
                });
            }
            dropdown.classList.add('show');
        }

        function selectMember(m) {
            hiddenInput.value = m.id;
            msName.textContent = m.nama + ' \u2014 ' + m.kode_member;
            msMeta.textContent = 'Aktif s/d: ' + formatTgl(m.tanggal_kadaluarsa);
            selectedCard.classList.add('show');
            searchInput.style.display = 'none';
            dropdown.classList.remove('show');
        }

        window.clearMember = function() {
            hiddenInput.value = '';
            searchInput.value = '';
            searchInput.style.display = '';
            selectedCard.classList.remove('show');
            dropdown.classList.remove('show');
            searchInput.focus();
        };

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var q = this.value.trim().toLowerCase();
                if (q.length < 1) {
                    dropdown.classList.remove('show');
                    return;
                }
                var results = allMembers.filter(function(m) {
                    return m.nama.toLowerCase().includes(q) ||
                        m.kode_member.toLowerCase().includes(q) ||
                        (m.no_wa && m.no_wa.includes(q));
                }).slice(0, 8);
                renderDropdown(results);
            });

            searchInput.addEventListener('blur', function() {
                setTimeout(function() {
                    dropdown.classList.remove('show');
                }, 150);
            });

            searchInput.addEventListener('focus', function() {
                if (this.value.trim().length > 0) {
                    this.dispatchEvent(new Event('input'));
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            switchTab(defaultTab);
            if (defaultTab === 'perpanjang' && preSelectedId && searchInput) {
                var found = allMembers.find(function(m) {
                    return m.id == preSelectedId;
                });
                if (found) selectMember(found);
            }
        });

    })();
</script>
@endpush