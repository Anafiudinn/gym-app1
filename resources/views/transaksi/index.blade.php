@extends('layouts.admin')
@section('title', 'Transaksi Onsite')
@section('page-title', 'Transaksi Onsite')

@push('styles')
<style>
    /* ── Tabs ── */
    .tab-btn {
        flex: 1; padding: 10px 6px;
        font-size: 11.5px; font-weight: 600; color: #94a3b8;
        border-bottom: 2px solid transparent;
        border-top: none; border-left: none; border-right: none;
        background: none; cursor: pointer;
        transition: color 0.15s, border-color 0.15s;
        white-space: nowrap; font-family: inherit;
    }
    .tab-btn:hover { color: #475569; }
    .tab-btn.active { color: #10b981; border-bottom-color: #10b981; }

    /* ── Form elements ── */
    .form-label {
        display: block; font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: #94a3b8; margin-bottom: 5px;
    }
    .form-input {
        width: 100%; border: 1px solid #e2e8f0; border-radius: 9px;
        padding: 8px 12px; font-size: 12.5px; color: #1e293b;
        background: #fff; outline: none; font-family: inherit;
        transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box;
    }
    .form-input:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.08);
    }

    /* ── Payment method radio ── */
    .pay-option { cursor: pointer; }
    .pay-option input[type="radio"] { display: none; }
    .pay-card {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        padding: 8px 10px; border-radius: 9px;
        border: 1.5px solid #e2e8f0; background: #fff;
        font-size: 11.5px; font-weight: 700; color: #64748b;
        transition: all 0.15s;
    }
    .pay-option input:checked ~ .pay-card.cash {
        border-color: #10b981; background: rgba(16,185,129,0.08); color: #059669;
    }
    .pay-option input:checked ~ .pay-card.transfer {
        border-color: #3b82f6; background: rgba(59,130,246,0.08); color: #2563eb;
    }
    .pay-card:hover { background: #f8fafc; }

    /* ── Submit buttons ── */
    .btn-primary {
        width: 100%; background: #10b981; color: #fff;
        font-size: 12.5px; font-weight: 700; padding: 10px;
        border-radius: 9px; border: none; cursor: pointer;
        transition: background 0.15s, transform 0.1s; font-family: inherit;
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .btn-primary:hover { background: #059669; }
    .btn-primary:active { transform: scale(0.98); }

    .btn-secondary {
        width: 100%; background: rgba(16,185,129,0.08); color: #059669;
        font-size: 12.5px; font-weight: 700; padding: 10px;
        border-radius: 9px; border: 1px solid rgba(16,185,129,0.2); cursor: pointer;
        transition: all 0.15s; font-family: inherit;
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .btn-secondary:hover { background: rgba(16,185,129,0.14); }

    /* ── Member search dropdown ── */
    .member-search-wrapper { position: relative; }
    .member-search-input {
        width: 100%; border: 1px solid #e2e8f0; border-radius: 9px;
        padding: 8px 12px 8px 34px; font-size: 12.5px; color: #1e293b;
        background: #fff; outline: none; font-family: inherit;
        transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box;
    }
    .member-search-input:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.08);
    }
    .member-search-icon {
        position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 11px; pointer-events: none;
    }
    .member-dropdown {
        position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08); z-index: 50;
        max-height: 200px; overflow-y: auto; display: none;
    }
    .member-dropdown.show { display: block; }
    .member-option {
        padding: 9px 12px; font-size: 12px; cursor: pointer;
        border-bottom: 1px solid #f8fafc; transition: background 0.1s;
        position: relative;
    }
    .member-option:last-child { border-bottom: none; }
    .member-option:hover { background: rgba(16,185,129,0.05); }
    .member-option .member-name { font-weight: 600; color: #1e293b; font-size: 12px; }
    .member-option .member-code { font-size: 10px; color: #94a3b8; margin-top: 1px; }
    .member-option .member-status {
        font-size: 9px; font-weight: 700; padding: 1px 6px;
        border-radius: 99px; float: right; margin-top: 1px;
    }
    .member-option .member-status.aktif { background: rgba(16,185,129,0.1); color: #059669; }
    .member-option .member-status.expired { background: #fef2f2; color: #dc2626; }
    .no-member-found { padding: 14px; font-size: 12px; color: #94a3b8; text-align: center; }

    .member-selected-card {
        background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.18);
        border-radius: 9px; padding: 9px 12px; margin-top: 7px; display: none;
    }
    .member-selected-card.show { display: block; }
    .ms-name { font-size: 12.5px; font-weight: 700; color: #065f46; }
    .ms-meta { font-size: 10.5px; color: #34d399; margin-top: 2px; }

    /* ── Table badges ── */
    .tipe-badge {
        display: inline-flex; align-items: center;
        padding: 2px 7px; border-radius: 99px;
        font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .tipe-membership { background: rgba(59,130,246,0.08); color: #2563eb; }
    .tipe-harian { background: #f1f5f9; color: #64748b; }

    .metode-badge {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 2px 7px; border-radius: 99px;
        font-size: 9px; font-weight: 700; text-transform: uppercase;
    }
    .metode-transfer { background: rgba(59,130,246,0.08); color: #2563eb; }
    .metode-cash { background: rgba(16,185,129,0.08); color: #059669; }

    .status-text-dibayar { color: #10b981; }
    .status-text-pending  { color: #f97316; }
    .status-text-batal    { color: #ef4444; }

    /* ── Section header inside form card ── */
    .section-divider {
        background: #f8fafc; border-radius: 8px; padding: 10px 12px;
        border: 1px solid #f1f5f9; margin-bottom: 2px;
    }
</style>
@endpush

@section('content')

{{-- Validation errors --}}
@if($errors->any())
<div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-[12.5px]">
    <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
    <div class="space-y-0.5">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    {{-- ═══ KIRI ═══ --}}
    <div class="lg:col-span-4 space-y-4 order-1">

        {{-- Summary card --}}
        <div class="bg-white rounded-2xl border border-gray-100 px-5 py-4 flex items-center justify-between" style="box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pemasukan Hari Ini</p>
                <p class="text-[22px] font-black text-gray-900 leading-none">Rp{{ number_format($totalHariIni, 0, ',', '.') }}</p>
                <p class="text-[10.5px] text-gray-400 mt-1.5">{{ $countHariIni }} transaksi selesai</p>
            </div>
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.1);">
                <i class="fa-solid fa-sack-dollar text-emerald-500 text-[16px]"></i>
            </div>
        </div>

        {{-- Form card --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,0.04);">

            {{-- Tabs --}}
            <div class="flex border-b border-gray-100">
                <button type="button" onclick="switchTab('tamu')" id="btn-tamu" class="tab-btn active">
                    <i class="fa-solid fa-person-walking text-[9px] mr-1"></i>Tamu
                </button>
                <button type="button" onclick="switchTab('member-baru')" id="btn-member-baru" class="tab-btn" style="border-left:1px solid #f1f5f9; border-right:1px solid #f1f5f9;">
                    <i class="fa-solid fa-user-plus text-[9px] mr-1"></i>Member Baru
                </button>
                <button type="button" onclick="switchTab('perpanjang')" id="btn-perpanjang" class="tab-btn">
                    <i class="fa-solid fa-rotate-right text-[9px] mr-1"></i>Perpanjang
                </button>
            </div>

            <div class="p-4 space-y-4">

                {{-- ── TAB TAMU ── --}}
                <div id="pane-tamu">
                    {{-- Paket info --}}
                    <div class="flex items-center justify-between rounded-xl px-4 py-3 mb-3" style="background:rgba(16,185,129,0.07); border:1px solid rgba(16,185,129,0.15);">
                        <div>
                            <p class="text-[9.5px] font-bold text-emerald-600 uppercase tracking-widest">Paket Default</p>
                            <p class="text-[13.5px] font-bold text-emerald-900 mt-0.5">{{ $paketDefault->nama_paket ?? 'Harian' }}</p>
                        </div>
                        <span class="text-[18px] font-black text-emerald-600">Rp{{ number_format($paketDefault->harga ?? 0, 0, ',', '.') }}</span>
                    </div>

                    <form method="POST" action="{{ route('transaksi.harian') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="form-label">Nama Pengunjung</label>
                            <input type="text" name="nama_tamu" placeholder="Masukkan nama tamu..." class="form-input" required>
                        </div>

                        <div>
                            <label class="form-label">Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                <label class="pay-option">
                                    <input type="radio" name="metode_pembayaran" value="cash" checked required>
                                    <div class="pay-card cash"><i class="fa-solid fa-money-bill-wave text-[11px]"></i> Cash</div>
                                </label>
                                <label class="pay-option">
                                    <input type="radio" name="metode_pembayaran" value="transfer">
                                    <div class="pay-card transfer"><i class="fa-solid fa-mobile-screen text-[11px]"></i> Transfer</div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-cash-register text-[11px]"></i> Bayar Sekarang
                        </button>
                    </form>
                </div>

                {{-- ── TAB MEMBER BARU ── --}}
                <div id="pane-member-baru" class="hidden">
                    <form method="POST" action="/transaksi/membership" class="space-y-3">
                        @csrf
                        <input type="hidden" name="tipe_member" value="baru">

                        <div class="section-divider space-y-3">
                            <div>
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" placeholder="Nama lengkap member" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">No. WhatsApp</label>
                                <input type="tel" name="no_wa" placeholder="08123456789" class="form-input"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                                <p class="text-[10px] text-gray-400 mt-1">Format angka saja (08...)</p>
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
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                <label class="pay-option">
                                    <input type="radio" name="metode_pembayaran" value="cash" checked required>
                                    <div class="pay-card cash"><i class="fa-solid fa-money-bill-wave text-[11px]"></i> Cash</div>
                                </label>
                                <label class="pay-option">
                                    <input type="radio" name="metode_pembayaran" value="transfer">
                                    <div class="pay-card transfer"><i class="fa-solid fa-mobile-screen text-[11px]"></i> Transfer</div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn-secondary" onclick="this.disabled=true; this.form.submit();">
                            <i class="fa-solid fa-id-card text-[11px]"></i> Daftarkan Member
                        </button>
                    </form>
                </div>

                {{-- ── TAB PERPANJANG ── --}}
                <div id="pane-perpanjang" class="hidden">
                    <form method="POST" action="/transaksi/membership" class="space-y-3">
                        @csrf
                        <input type="hidden" name="tipe_member" value="perpanjang">

                        <div>
                            <label class="form-label">Cari Member</label>
                            <input type="hidden" name="member_id" id="member_id_hidden">

                            <div class="member-search-wrapper">
                                <i class="fa-solid fa-magnifying-glass member-search-icon"></i>
                                <input type="text" id="member_search_input"
                                    class="member-search-input"
                                    placeholder="Ketik nama atau kode member..."
                                    autocomplete="off">
                                <div class="member-dropdown" id="member_dropdown"></div>
                            </div>

                            <div class="member-selected-card" id="member_selected_card">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="ms-name truncate" id="ms-name"></div>
                                        <div class="ms-meta" id="ms-meta"></div>
                                    </div>
                                    <button type="button" onclick="clearMember()"
                                        class="text-[10px] text-red-400 hover:text-red-600 font-semibold flex-shrink-0 flex items-center gap-1">
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
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                <label class="pay-option">
                                    <input type="radio" name="metode_pembayaran" value="cash" checked required>
                                    <div class="pay-card cash"><i class="fa-solid fa-money-bill-wave text-[11px]"></i> Cash</div>
                                </label>
                                <label class="pay-option">
                                    <input type="radio" name="metode_pembayaran" value="transfer">
                                    <div class="pay-card transfer"><i class="fa-solid fa-mobile-screen text-[11px]"></i> Transfer</div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn-secondary">
                            <i class="fa-solid fa-rotate-right text-[11px]"></i> Perpanjang Membership
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══ KANAN ═══ --}}
    <div class="lg:col-span-8 space-y-4 order-2">

        {{-- Filter bar --}}
        <div class="bg-white rounded-2xl border border-gray-100 px-4 py-3.5" style="box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <form action="{{ route('transaksi.index') }}" method="GET">
                @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}"> @endif
                <div class="flex flex-wrap gap-2">
                    <div class="flex-1 min-w-[150px] relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama / invoice..."
                            class="form-input pl-8" style="padding-top:7px; padding-bottom:7px;">
                    </div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="form-input" style="width:135px; padding:7px 10px;">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="form-input" style="width:135px; padding:7px 10px;">
                    <select name="tipe" class="form-input" style="width:120px; padding:7px 10px;">
                        <option value="">Semua Tipe</option>
                        <option value="harian" {{ request('tipe')==='harian' ? 'selected' : '' }}>Harian</option>
                        <option value="membership" {{ request('tipe')==='membership' ? 'selected' : '' }}>Membership</option>
                    </select>
                    <select name="status" class="form-input" style="width:120px; padding:7px 10px;">
                        <option value="">Semua Status</option>
                        <option value="dibayar" {{ request('status')==='dibayar' ? 'selected' : '' }}>Dibayar</option>
                        <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                        <option value="batal" {{ request('status')==='batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                    <button type="submit"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1.5">
                        <i class="fa-solid fa-filter text-[10px]"></i> Filter
                    </button>
                    @if(request()->hasAny(['search','date_from','date_to','tipe','status']))
                    <a href="{{ route('transaksi.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-500 text-[12px] font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1.5">
                        <i class="fa-solid fa-xmark text-[10px]"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,0.04);">

            <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-800 text-[13px]">Riwayat Transaksi Onsite</h3>
                    <p class="text-[10.5px] text-gray-400 mt-0.5">
                        <i class="fa-regular fa-calendar-days mr-1"></i>{{ now()->translatedFormat('d F Y') }}
                    </p>
                </div>
                <div class="flex items-center gap-1.5 text-[10.5px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span> Live
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px]">
                    <thead>
                        <tr class="bg-gray-50/70">
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Bayar</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($data as $d)
                        <tr class="hover:bg-gray-50/60 transition-colors group">
                            <td class="px-4 py-3.5 text-[11.5px] text-gray-400">
                                {{ $data->firstItem() + $loop->index }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-[11px] font-bold text-gray-700 group-hover:text-emerald-600 transition">
                                    {{ $d->kode_invoice }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-[12px] font-semibold text-gray-700 leading-tight">{{ $d->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $d->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-[12.5px] font-semibold text-gray-800 leading-tight">
                                    {{ $d->member->nama ?? $d->nama_tamu }}
                                </div>
                                <span class="tipe-badge mt-1 {{ $d->tipe === 'membership' ? 'tipe-membership' : 'tipe-harian' }}">
                                    {{ $d->tipe }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="metode-badge {{ $d->metode_pembayaran === 'transfer' ? 'metode-transfer' : 'metode-cash' }}">
                                    @if($d->metode_pembayaran === 'transfer')
                                        <i class="fa-solid fa-mobile-screen text-[8px]"></i>
                                    @else
                                        <i class="fa-solid fa-money-bill-wave text-[8px]"></i>
                                    @endif
                                    {{ $d->metode_pembayaran ?? 'cash' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="text-[13px] font-black text-gray-900">Rp{{ number_format($d->jumlah_bayar, 0, ',', '.') }}</div>
                                <span class="text-[9.5px] font-bold uppercase status-text-{{ $d->status }}">{{ $d->status }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('transaksi.struk', $d->id) }}" target="_blank"
                                       class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition"
                                       title="Cetak Struk">
                                        <i class="fa-solid fa-print text-[10px]"></i>
                                    </a>

                                    @if($d->status !== 'batal')
                                    <form action="{{ route('transaksi.batalkan', $d->id) }}" method="POST"
                                          onsubmit="return batalkanConfirm(event, this)">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition"
                                            title="Batalkan Transaksi">
                                            <i class="fa-solid fa-ban text-[10px]"></i>
                                        </button>
                                    </form>
                                    @else
                                    <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-50 text-gray-300 border border-gray-100 cursor-not-allowed" title="Sudah dibatalkan">
                                        <i class="fa-solid fa-ban text-[10px]"></i>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-receipt text-xl text-gray-300"></i>
                                </div>
                                <p class="text-[12px] text-gray-400 font-medium">Tidak ada transaksi ditemukan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->hasPages())
            <div class="px-5 py-3 border-t border-gray-50 flex items-center justify-between flex-wrap gap-2">
                <p class="text-[11px] text-gray-400">
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
(function () {
    @php
        $membersJson = $members->map(fn($m) => [
            'id'                  => $m->id,
            'nama'                => $m->nama,
            'kode_member'         => $m->kode_member,
            'no_wa'               => $m->no_wa ?? '',
            'tanggal_kadaluarsa'  => $m->tanggal_kadaluarsa,
            'status'              => $m->status,
        ]);
    @endphp

    var allMembers   = @json($membersJson);
    var defaultTab   = @js($activeTab ?? 'tamu');
    var preSelectedId = @js($selectedMemberId ?? null);
    var TABS         = ['tamu', 'member-baru', 'perpanjang'];

    /* ── Tab switching ── */
    function switchTab(tab) {
        TABS.forEach(function (t) {
            var pane = document.getElementById('pane-' + t);
            var btn  = document.getElementById('btn-' + t);
            if (pane) pane.classList.toggle('hidden', t !== tab);
            if (btn)  btn.classList.toggle('active', t === tab);
        });
    }
    window.switchTab = switchTab;

    /* ── Member search ── */
    var searchInput  = document.getElementById('member_search_input');
    var dropdown     = document.getElementById('member_dropdown');
    var hiddenInput  = document.getElementById('member_id_hidden');
    var selectedCard = document.getElementById('member_selected_card');
    var msName       = document.getElementById('ms-name');
    var msMeta       = document.getElementById('ms-meta');

    function formatTgl(tgl) {
        if (!tgl) return 'Belum ada data';
        var d = new Date(tgl);
        var bln = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        return d.getDate() + ' ' + bln[d.getMonth()] + ' ' + d.getFullYear();
    }

    function renderDropdown(results) {
        dropdown.innerHTML = '';
        if (!results.length) {
            dropdown.innerHTML = '<div class="no-member-found"><i class="fa-solid fa-user-slash mr-1"></i> Member tidak ditemukan</div>';
        } else {
            results.forEach(function (m) {
                var expired     = m.tanggal_kadaluarsa && new Date(m.tanggal_kadaluarsa) < new Date();
                var statusClass = (m.status === 'aktif' && !expired) ? 'aktif' : 'expired';
                var el          = document.createElement('div');
                el.className    = 'member-option';
                el.innerHTML    =
                    '<span class="member-status ' + statusClass + '">' + (expired ? 'expired' : m.status) + '</span>' +
                    '<div class="member-name">' + m.nama + '</div>' +
                    '<div class="member-code">' + m.kode_member + (m.no_wa ? ' · ' + m.no_wa : '') + '</div>';
                el.addEventListener('mousedown', function (e) { e.preventDefault(); selectMember(m); });
                dropdown.appendChild(el);
            });
        }
        dropdown.classList.add('show');
    }

    function selectMember(m) {
        hiddenInput.value       = m.id;
        msName.textContent      = m.nama + ' — ' + m.kode_member;
        msMeta.textContent      = 'Aktif s/d: ' + formatTgl(m.tanggal_kadaluarsa);
        selectedCard.classList.add('show');
        searchInput.style.display = 'none';
        dropdown.classList.remove('show');
    }

    window.clearMember = function () {
        hiddenInput.value         = '';
        searchInput.value         = '';
        searchInput.style.display = '';
        selectedCard.classList.remove('show');
        dropdown.classList.remove('show');
        searchInput.focus();
    };

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = this.value.trim().toLowerCase();
            if (q.length < 1) { dropdown.classList.remove('show'); return; }
            var results = allMembers.filter(function (m) {
                return m.nama.toLowerCase().includes(q) ||
                       m.kode_member.toLowerCase().includes(q) ||
                       (m.no_wa && m.no_wa.includes(q));
            }).slice(0, 8);
            renderDropdown(results);
        });
        searchInput.addEventListener('blur',  function () { setTimeout(function () { dropdown.classList.remove('show'); }, 150); });
        searchInput.addEventListener('focus', function () { if (this.value.trim().length > 0) this.dispatchEvent(new Event('input')); });
    }

    /* ── Batalkan confirm ── */
    window.batalkanConfirm = function (e, form) {
        e.preventDefault();
        Swal.confirm({
            title: 'Batalkan Transaksi?',
            text: 'Pendapatan akan dikurangi dan transaksi tidak dapat dikembalikan.',
            confirmText: 'Ya, Batalkan',
            onConfirm: function () { form.submit(); }
        });
    };

    /* ── Init ── */
    document.addEventListener('DOMContentLoaded', function () {
        switchTab(defaultTab);
        if (defaultTab === 'perpanjang' && preSelectedId && searchInput) {
            var found = allMembers.find(function (m) { return m.id == preSelectedId; });
            if (found) selectMember(found);
        }
    });
})();
</script>
@endpush