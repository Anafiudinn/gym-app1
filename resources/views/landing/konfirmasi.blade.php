@extends('layouts.landing')

@push('styles')
<style>
    .konfirmasi-page {
        min-height: calc(100vh - 60px);
        padding: 3rem 1rem 4rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .konfirmasi-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 20px;
        padding: 2.5rem;
        width: 100%;
        max-width: 520px;
        text-align: center;
    }

    .success-icon {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: rgba(170,255,0,0.1);
        border: 2px solid var(--lime);
        display: grid;
        place-items: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
    }

    .konfirmasi-card h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--lime);
        margin-bottom: 0.5rem;
    }

    .konfirmasi-card .desc {
        color: var(--muted);
        font-size: 0.875rem;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .detail-rows {
        background: var(--bg3);
        border-radius: 14px;
        overflow: hidden;
        text-align: left;
        margin-bottom: 1.75rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem 1.25rem;
        border-bottom: 1px solid var(--border);
        font-size: 0.875rem;
    }

    .detail-row:last-child { border-bottom: none; }
    .detail-row .dl { color: var(--muted); }
    .detail-row .dv { font-weight: 700; }
    .detail-row .dv.code { color: var(--lime); font-family: monospace; }

    .member-card-mini {
        background: linear-gradient(135deg, #1a2e0a, #0f1f06);
        border: 1.5px solid rgba(170,255,0,0.3);
        border-radius: 14px;
        padding: 1.25rem;
        text-align: left;
        margin-bottom: 1.75rem;
    }

    .mcm-top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .mcm-brand { font-weight: 800; color: var(--lime); font-size: 0.85rem; letter-spacing: 1px; }

    .mcm-kode {
        font-family: monospace;
        font-size: 0.85rem;
        color: var(--lime);
        background: rgba(170,255,0,0.1);
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
    }

    .mcm-nama { font-weight: 800; font-size: 1.1rem; margin-bottom: 0.25rem; }
    .mcm-paket { font-size: 0.8rem; color: var(--muted); }

    .btn-home {
        display: block;
        background: var(--lime);
        color: #000;
        padding: 0.85rem;
        border-radius: 12px;
        font-weight: 800;
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.2s;
        width: 100%;
        text-align: center;
    }

    .btn-home:hover { background: var(--lime-dark); }

    .tip-box {
        background: rgba(170,255,0,0.04);
        border: 1px solid rgba(170,255,0,0.1);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.8rem;
        color: var(--muted);
        text-align: left;
    }

    .tip-box strong { color: var(--lime); }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="konfirmasi-page">
        <div style="text-align:center;margin-bottom:2rem;">
            <h1 style="font-size:clamp(1.6rem,3vw,2.2rem);font-weight:800;">Layanan <span style="color:var(--lime);">Kami</span></h1>
            <p style="color:var(--muted);font-size:0.875rem;margin-top:0.3rem;">Pilih layanan yang kamu butuhkan</p>
        </div>

        <!-- Step Indicator -->
        <div class="steps" style="margin-bottom:2rem;">
            <div class="step-circle done">1</div>
            <div class="step-line active"></div>
            <div class="step-circle done">2</div>
            <div class="step-line active"></div>
            <div class="step-circle active">3</div>
        </div>

        <div class="konfirmasi-card">
            <div class="success-icon">✓</div>
            <h2>Pembayaran Terkonfirmasi!</h2>
            <p class="desc">
                Selamat! Kamu kini resmi menjadi member JefryGym.<br>
                Tunjukkan kode member kamu saat masuk gym.
            </p>

            <div class="member-card-mini">
                <div class="mcm-top">
                    <div class="mcm-brand">JEFRYGYM</div>
                    <div class="mcm-kode">{{ $transaksi->member->kode_member }}</div>
                </div>
                <div class="mcm-nama">{{ $transaksi->member->nama }}</div>
                <div class="mcm-paket">Paket {{ $transaksi->paket->nama }}</div>
            </div>

            <div class="detail-rows">
                <div class="detail-row">
                    <span class="dl">Kode Invoice</span>
                    <span class="dv code">{{ $transaksi->kode_invoice }}</span>
                </div>
                <div class="detail-row">
                    <span class="dl">No. WhatsApp</span>
                    <span class="dv">{{ $transaksi->member->no_wa }}</span>
                </div>
                <div class="detail-row">
                    <span class="dl">Paket</span>
                    <span class="dv">{{ $transaksi->paket->nama }}</span>
                </div>
                <div class="detail-row">
                    <span class="dl">Total Dibayar</span>
                    <span class="dv" style="color:var(--lime);">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span class="dl">Tanggal</span>
                    <span class="dv">{{ \Carbon\Carbon::parse($transaksi->updated_at)->format('d/m/Y') }}</span>
                </div>
            </div>

            <div class="tip-box">
                <strong>💡 Tips:</strong> Simpan kode member kamu: <strong>{{ $transaksi->member->kode_member }}</strong> untuk cek status membership kapan saja di menu Cek Membership.
            </div>

            <a href="/" class="btn-home">Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection