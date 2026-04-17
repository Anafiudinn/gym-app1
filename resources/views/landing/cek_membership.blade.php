@extends('layouts.landing')

@push('styles')
<style>
    .layanan-page {
        min-height: calc(100vh - 60px);
        padding: 3rem 1rem 4rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .cek-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 20px;
        padding: 2.25rem;
        width: 100%;
        max-width: 520px;
    }

    .cek-card h2 {
        font-size: 1.3rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }

    .cek-card .subtitle {
        font-size: 0.85rem;
        color: var(--muted);
        margin-bottom: 1.75rem;
    }

    .search-row {
        display: flex;
        gap: 0.6rem;
    }

    .search-wrap {
        position: relative;
        flex: 1;
    }

    .search-icon {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
    }

    .search-input {
        width: 100%;
        background: var(--bg3);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.72rem 1rem 0.72rem 2.5rem;
        color: var(--text);
        font-family: 'Outfit', sans-serif;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .search-input:focus {
        border-color: var(--lime);
    }

    .search-input::placeholder {
        color: #444;
    }

    .btn-cari {
        background: var(--lime);
        color: #000;
        border: none;
        padding: 0 1.5rem;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.875rem;
        cursor: pointer;
        font-family: 'Outfit', sans-serif;
        transition: 0.2s;
    }

    .btn-cari:hover {
        background: var(--lime-dark);
    }

    /* Member Card */
    .member-card {
        background: linear-gradient(135deg, #1a2e0a 0%, #0f1f06 100%);
        border: 1.5px solid rgba(170, 255, 0, 0.3);
        border-radius: 16px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .member-card::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(170, 255, 0, 0.05);
    }

    .mc-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .mc-brand {
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--lime);
        letter-spacing: 1px;
    }

    .mc-status {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
    }

    .mc-status.aktif {
        background: rgba(170, 255, 0, 0.15);
        color: var(--lime);
    }

    .mc-status.nonaktif {
        background: rgba(255, 68, 68, 0.1);
        color: var(--danger);
    }

    .mc-icon {
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
        opacity: 0.3;
    }

    .mc-field {
        margin-bottom: 0.6rem;
    }

    .mc-label {
        font-size: 0.72rem;
        color: rgba(170, 255, 0, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .mc-value {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--text);
    }

    .mc-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(170, 255, 0, 0.1);
    }

    .mc-paket .mc-label {
        color: rgba(255, 255, 255, 0.4);
    }

    .mc-paket .mc-value {
        font-size: 0.9rem;
    }

    .mc-aktif-sd .mc-label {
        text-align: right;
        color: rgba(255, 255, 255, 0.4);
    }

    .mc-aktif-sd .mc-value {
        color: var(--lime);
        text-align: right;
    }

    /* Riwayat */
    .riwayat-list {
        margin-top: 1.5rem;
    }

    .riwayat-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.75rem;
    }

    .riwayat-item {
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.8rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .riwayat-item .ri-left .ri-paket {
        font-size: 0.875rem;
        font-weight: 700;
    }

    .riwayat-item .ri-left .ri-tgl {
        font-size: 0.75rem;
        color: var(--muted);
    }

    .riwayat-item .ri-harga {
        font-weight: 700;
        color: var(--lime);
        font-size: 0.875rem;
    }

    /* Tambahkan ini untuk efek hover pada link kembali */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--muted);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 1.25rem;
        transition: all 0.2s;
    }

    .back-link:hover {
        color: var(--lime);
        transform: translateX(-4px);
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="layanan-page">
        <div style="text-align:center;margin-bottom:2rem;">
            <h1 style="font-size:clamp(1.6rem,3vw,2.2rem);font-weight:800;">Layanan <span style="color:var(--lime);">Kami</span></h1>
            <p style="color:var(--muted);font-size:0.875rem;margin-top:0.3rem;">Pilih layanan yang kamu butuhkan</p>
        </div>


        <a href="/#layanan" style="display:inline-flex;align-items:center;gap:0.4rem;color:var(--muted);text-decoration:none;font-size:0.85rem;font-weight:500;margin-bottom:1.75rem;transition:color 0.2s;align-self:flex-start;width:100%;max-width:520px;">
            ← Kembali ke layanan
        </a>
        <div class="cek-card">
            <h2>Cek Membership</h2>
            <p class="subtitle">Masukkan kode member atau nomor WhatsApp</p>

            @if(session('error'))
            <div class="alert alert-error" style="margin-bottom:1.25rem;">{{ session('error') }}</div>
            @endif

            <form method="GET" action="/cek-membership">
                <div class="search-row">
                    <div class="search-wrap">
                        <span class="search-icon">🔍</span>
                        <input type="text" name="search" class="search-input"
                            placeholder="MBR-XXXX atau No WA"
                            value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-cari">Cari</button>
                </div>
            </form>

            @isset($member)
            @php
            $aktifTranaksi = $member->transaksi()->where('status','dibayar')->latest()->first();
            $tanggalAktif = $aktifTranaksi ? \Carbon\Carbon::parse($aktifTranaksi->created_at)->addMonth() : null;
            $riwayat = $member->transaksi()->where('status','dibayar')->latest()->get();
            @endphp

            <div class="member-card">
                <div class="mc-top">
                    <div class="mc-brand">JEFRYGYM</div>
                    <div class="mc-status {{ $member->status === 'aktif' ? 'aktif' : 'nonaktif' }}">
                        {{ ucfirst($member->status) }}
                    </div>
                </div>

                <div class="mc-icon">💳</div>

                <div class="mc-field">
                    <div class="mc-label">Member ID</div>
                    <div class="mc-value" style="color:var(--lime);">{{ $member->kode_member }}</div>
                </div>

                <div class="mc-field">
                    <div class="mc-label">Nama</div>
                    <div class="mc-value" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>{{ $member->nama }}</span>
                        <span style="font-size: 0.75rem; color: var(--muted); font-weight: 400;">
                            <i class="fab fa-whatsapp"></i> {{ $member->no_wa }}
                        </span>
                    </div>
                </div>

                <div class="mc-footer">
                    <div class="mc-paket">
                        <div class="mc-label">Paket</div>
                        <div class="mc-value">{{ $aktifTranaksi?->paket?->nama_paket ?? '-' }}</div>
                    </div>
                    @if($tanggalAktif)
                    <div class="mc-aktif-sd">
                        <div class="mc-label">Aktif s/d</div>
                        <div class="mc-value">{{ $tanggalAktif->format('d/m/Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>

        
        <div style="margin-top: 1rem; text-align: center;">
            <button id="downloadCard" class="btn-cari" style="background: var(--lime); color: #000; width: 100%; max-width: 600px; height: 1.5rem;">
                Download Kartu Digital
            </button>
        </div>

        @if($riwayat->count() > 0)
        <div class="riwayat-list">
            <div class="riwayat-title">Riwayat Pembelian</div>
            @foreach($riwayat as $r)
            <div class="riwayat-item">
                <div class="ri-left">
                    <div class="ri-paket">{{ $r->paket?->nama_paket ?? 'Paket' }}</div>
                    <div class="ri-tgl">{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y') }}</div>
                </div>
                <div class="ri-harga">Rp {{ number_format($r->jumlah_bayar, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
        @endif

        @endisset
    </div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    document.getElementById('downloadCard').addEventListener('click', function() {
        const card = document.querySelector('.member-card');
        const btn = this;
        
        // Ubah teks tombol saat proses
        const originalText = btn.textContent;
        btn.textContent = '⏳ Memproses...';
        btn.disabled = true;

        html2canvas(card, {
            scale: 2, // Biar hasil gambar lebih tajam/HD
            backgroundColor: null, // Background transparan jika border-radius ada
            useCORS: true // Jaga-jaga kalau ada gambar dari URL luar
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'Member-{{ $member->kode_member }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
            
            // Kembalikan tombol
            btn.textContent = originalText;
            btn.disabled = false;
        }).catch(err => {
            console.error('Gagal download kartu:', err);
            btn.textContent = '❌ Gagal Download';
            btn.disabled = false;
        });
    });
</script>
@endsection