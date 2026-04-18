@extends('layouts.admin')
@section('title', 'Detail Member')
@section('page-title', 'Detail Member')

@push('styles')
<style>
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-up {
        animation: fadeUp .32s ease both;
    }

    .delay-1 {
        animation-delay: .04s;
    }

    .delay-2 {
        animation-delay: .09s;
    }

    .delay-3 {
        animation-delay: .14s;
    }

    /* ── CARD BASE ── */
    .ui-card {
        background: #fff;
        border: 1px solid #eaecf4;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(30, 33, 57, .04);
        overflow: hidden;
    }

    .ui-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid #f0f2f9;
    }

    .ui-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e2139;
    }

    .ui-card-icon {
        font-size: 16px;
        color: #d1d5f0;
    }

    /* ── MEMBERSHIP HERO CARD ── */
    .hero-card {
        border-radius: 16px;
        padding: 26px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        box-shadow: 0 8px 28px rgba(99, 102, 241, .30);
        color: #fff;
    }

    .hero-card::before {
        content: '';
        position: absolute;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, .06);
        border-radius: 50%;
        top: -60px;
        right: -40px;
    }

    .hero-card::after {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, .05);
        border-radius: 50%;
        bottom: -30px;
        left: 20px;
    }

    /* ── STAT MINI INSIDE HERO ── */
    .hero-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 22px;
        position: relative;
        z-index: 1;
    }

    .hero-item {}

    .hero-item-label {
        font-size: 10px;
        font-weight: 600;
        opacity: .65;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .hero-item-val {
        font-size: 13px;
        font-weight: 700;
        margin-top: 3px;
    }

    /* ── BADGE STATUS ── */
    .hero-status {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        background: rgba(255, 255, 255, .2);
        backdrop-filter: blur(6px);
        letter-spacing: .06em;
        text-transform: uppercase;
        border: 1px solid rgba(255, 255, 255, .25);
    }

    /* ── ACTION BUTTONS ── */
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        padding: 10px 14px;
        border-radius: 9px;
        border: none;
        font-size: 13px;
        font-weight: 600;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
        transition: all .16s;
        text-decoration: none;
        line-height: 1;
    }

    .action-btn.primary {
        background: #6366f1;
        color: #fff;
    }

    .action-btn.primary:hover {
        background: #4f46e5;
        box-shadow: 0 4px 12px rgba(99, 102, 241, .30);
    }

    .action-btn.success {
        background: #22c55e;
        color: #fff;
    }

    .action-btn.success:hover {
        background: #16a34a;
        box-shadow: 0 4px 12px rgba(34, 197, 94, .25);
    }

    .action-btn.ghost {
        background: #fff;
        color: #4b5066;
        border: 1px solid #eaecf4;
    }

    .action-btn.ghost:hover {
        background: #f5f6fa;
        border-color: #d1d5f0;
    }

    /* ── TIMELINE ROWS ── */
    .timeline-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        border-bottom: 1px solid #f5f6fa;
        transition: background .12s;
    }

    .timeline-row:last-child {
        border-bottom: none;
    }

    .timeline-row:hover {
        background: #fafbff;
    }

    .timeline-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 13px;
    }

    /* ── EMPTY STATE ── */
    .empty-state {
        padding: 40px 20px;
        text-align: center;
    }

    .empty-state i {
        font-size: 30px;
        color: #e2e4f0;
    }

    .empty-state p {
        font-size: 13px;
        color: #a0a3b8;
        margin-top: 8px;
        font-weight: 500;
    }

    /* ── MODAL ── */
    .modal-bg {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 20, 50, .45);
        backdrop-filter: blur(4px);
        animation: fadeIn .18s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-box {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(15, 20, 50, .18);
        width: 100%;
        max-width: 420px;
        margin: 16px;
        overflow: hidden;
        animation: fadeUp .2s ease;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f2f9;
        background: #fafbff;
    }

    .modal-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e2139;
    }

    .modal-close {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        color: #8b8fa8;
        font-size: 13px;
        cursor: pointer;
        transition: all .14s;
    }

    .modal-close:hover {
        background: #f0f2f9;
        color: #1e2139;
    }

    .modal-body {
        padding: 20px;
    }

    /* ── FORM ELEMENTS (MODAL) ── */
    .form-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #8b8fa8;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: 5px;
    }

    .form-input {
        width: 100%;
        padding: 9px 12px;
        font-size: 13px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        border: 1px solid #eaecf4;
        border-radius: 8px;
        color: #1e2139;
        background: #f5f6fa;
        outline: none;
        transition: border-color .15s, background .15s;
    }

    .form-input:focus {
        border-color: #6366f1;
        background: #fff;
    }

    /* Back link */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 500;
        color: #8b8fa8;
        text-decoration: none;
        transition: color .14s;
        margin-bottom: 18px;
    }

    .back-link:hover {
        color: #6366f1;
    }
</style>
@endpush

@section('content')

{{-- Back --}}
<a href="{{ route('member.index') }}" class="back-link">
    <i class="fa-solid fa-chevron-left" style="font-size:10px;"></i> Kembali ke Daftar Member
</a>

<div style="display:grid; grid-template-columns:280px 1fr; gap:18px; align-items:start;" class="fade-up delay-1">

    {{-- ════════════════════════════════════════ --}}
    {{-- LEFT COLUMN                              --}}
    {{-- ════════════════════════════════════════ --}}
    <div style="display:flex; flex-direction:column; gap:14px;">

        {{-- ── HERO / MEMBERSHIP CARD ── --}}
        <div class="hero-card">
            {{-- Top row --}}
            <div style="display:flex; align-items:center; justify-content:space-between; position:relative; z-index:1;">
                <span style="font-size:10px; font-weight:700; opacity:.7; text-transform:uppercase; letter-spacing:.14em;">
                    GymPro · Membership
                </span>
                <span class="hero-status">{{ strtoupper($member->status) }}</span>
            </div>

            {{-- Avatar + Name --}}
            @php
            $colors = ['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f59e0b','#10b981','#06b6d4','#3b82f6'];
            $avatarBg = $colors[ord(strtoupper($member->nama[0])) % count($colors)];
            @endphp
            <div style="display:flex; align-items:center; gap:14px; margin-top:20px; position:relative; z-index:1;">
                <div style="width:50px; height:50px; border-radius:14px; background:rgba(255,255,255,.2);
                            border:2px solid rgba(255,255,255,.35);
                            display:flex; align-items:center; justify-content:center;
                            font-size:18px; font-weight:800; flex-shrink:0;">
                    {{ strtoupper(substr($member->nama, 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:17px; font-weight:800; line-height:1.2;">{{ $member->nama }}</div>
                    <div style="font-family:'Courier New',monospace; font-size:11px; opacity:.7; margin-top:3px;">
                        {{ $member->kode_member }}
                    </div>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="hero-grid">
                <div class="hero-item">
                    <div class="hero-item-label">Paket</div>
                    <div class="hero-item-val">{{ $member->membership?->paket?->nama_paket ?? '—' }}</div>
                </div>
                <div class="hero-item">
                    <div class="hero-item-label">WhatsApp</div>
                    <div class="hero-item-val">{{ $member->no_wa ?? '—' }}</div>
                </div>
                <div class="hero-item">
                    <div class="hero-item-label">Berlaku s/d</div>
                    <div class="hero-item-val">
                        {{ $member->tanggal_kadaluarsa
                            ? \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->format('d M Y')
                            : '—' }}
                    </div>
                </div>
                <div class="hero-item">
                    <div class="hero-item-label">Gender</div>
                    <div class="hero-item-val">{{ ucfirst($member->jenis_kelamin ?? '—') }}</div>
                </div>
            </div>
        </div>

        {{-- ── QUICK ACTIONS ── --}}
        <div class="ui-card" style="padding:16px; display:flex; flex-direction:column; gap:8px;">
            <p style="font-size:11px; font-weight:700; color:#a0a3b8; text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px;">
                Tindakan Cepat
            </p>

            <button onclick="document.getElementById('modal-edit').classList.remove('hidden')"
                class="action-btn primary">
                <i class="fa-solid fa-pen-to-square" style="font-size:11px;"></i> Edit Profil Member
            </button>

            <a href="{{ route('transaksi.index', ['tab'=>'perpanjang','member'=>$member->id]) }}"
                class="action-btn success">
                <i class="fa-solid fa-rotate" style="font-size:11px;"></i> Perpanjang Membership
            </a>

            <form method="POST" action="{{ route('member.toggle', $member->id) }}">
                @csrf @method('PATCH')
                <button type="submit" class="action-btn ghost" style="width:100%;">
                    <i class="fa-solid fa-power-off" style="font-size:11px;"></i>
                    {{ $member->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }} Member
                </button>
            </form>
        </div>

    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- RIGHT COLUMN                             --}}
    {{-- ════════════════════════════════════════ --}}
    <div style="display:flex; flex-direction:column; gap:14px;">

        {{-- ── RIWAYAT TRANSAKSI ── --}}
        <div class="ui-card fade-up delay-2">
            <div class="ui-card-header">
                <span class="ui-card-title">
                    <i class="fa-solid fa-receipt" style="color:#6366f1; margin-right:7px; font-size:13px;"></i>
                    5 Transaksi Terakhir
                </span>
                <i class="fa-solid fa-clock-rotate-left ui-card-icon"></i>
            </div>
            <div>
                @forelse($transaksi->take(5) as $trx)
                <div class="timeline-row">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div class="timeline-icon" style="background:#eef2ff; color:#6366f1;">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:600; color:#1e2139;">{{ $trx->tipe }}</div>
                            <div style="font-size:11px; color:#a0a3b8; margin-top:2px;">
                                {{ \Carbon\Carbon::parse($trx->tanggal_pembayaran)->format('d M Y') }}
                                @if($trx->paket?->nama_paket)
                                · {{ $trx->paket->nama_paket }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:13px; font-weight:700; color:#1e2139;">
                            Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}
                        </div>
                        <span style="font-size:10px; font-weight:700; color:#22c55e; text-transform:uppercase; letter-spacing:.06em;">
                            LUNAS
                        </span>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fa-solid fa-receipt"></i>
                    <p>Belum ada riwayat transaksi</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── RIWAYAT ABSENSI ── --}}
        <div class="ui-card fade-up delay-3">
            <div class="ui-card-header">
                <span class="ui-card-title">
                    <i class="fa-solid fa-fingerprint" style="color:#6366f1; margin-right:7px; font-size:13px;"></i>
                    5 Absensi Terakhir
                </span>
                <i class="fa-solid fa-calendar-check ui-card-icon"></i>
            </div>
            <div>
                @forelse($absensi as $absen)
                <div class="timeline-row">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div class="timeline-icon" style="background:#f0fdf4; color:#22c55e;">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:600; color:#1e2139;">
                                {{ \Carbon\Carbon::parse($absen->created_at)->translatedFormat('d M Y, l') }}
                            </div>
                            <div style="font-size:11px; color:#a0a3b8; margin-top:2px;">Hadir pada sesi latihan</div>
                        </div>
                    </div>
                    <span style="font-family:'Courier New',monospace; font-size:12px; font-weight:600;
                                 background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;
                                 padding:3px 10px; border-radius:7px;">
                        {{ \Carbon\Carbon::parse($absen->created_at)->format('H:i') }}
                    </span>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fa-solid fa-fingerprint"></i>
                    <p>Belum ada riwayat absensi</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>{{-- /right col --}}

</div>{{-- /grid --}}

{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL EDIT PROFILE                                       --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div id="modal-edit" class="modal-bg hidden">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Edit Profil Member</span>
            <button class="modal-close" onclick="document.getElementById('modal-edit').classList.add('hidden')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('member.update', $member->id) }}" class="modal-body">
            @csrf @method('PUT')
            <div style="display:flex; flex-direction:column; gap:14px;">

                <div>
                    <label class="form-label">Nama Lengkap</label>
                    <input class="form-input" type="text" name="nama" value="{{ $member->nama }}" required>
                </div>

                <div>
                    <label class="form-label">No. WhatsApp</label>
                    <input class="form-input" type="text" name="no_wa" value="{{ $member->no_wa }}">
                </div>

                <div style="display:flex; gap:8px; padding-top:4px;">
                    <button type="button"
                        onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="action-btn ghost" style="flex:1;">
                        Batal
                    </button>
                    <button type="submit" class="action-btn primary" style="flex:1;">
                        <i class="fa-solid fa-floppy-disk" style="font-size:12px;"></i> Simpan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Close modal on backdrop click
    document.getElementById('modal-edit').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
    // ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.getElementById('modal-edit').classList.add('hidden');
    });
</script>
@endpush

@endsection