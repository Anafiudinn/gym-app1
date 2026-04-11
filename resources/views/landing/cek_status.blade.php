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

    .layanan-container {
        width: 100%;
        max-width: 520px;
    }

    .cek-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 20px;
        padding: 2.25rem;
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

    .search-input {
        flex: 1;
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

    .search-input:focus { border-color: var(--lime); }
    .search-input::placeholder { color: #444; }

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
        font-size: 0.9rem;
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
        white-space: nowrap;
    }

    .btn-cari:hover { background: var(--lime-dark); }

    /* Result card */
    .result-pending {
        background: rgba(245,158,11,0.08);
        border: 1.5px solid rgba(245,158,11,0.3);
        border-radius: 14px;
        padding: 1.25rem;
        margin-top: 1.5rem;
    }

    .result-pending-header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 0.25rem;
    }

    .result-pending-header .title {
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--warning);
    }

    .result-pending-header .inv {
        font-size: 0.78rem;
        color: var(--muted);
    }

    .result-info-grid {
        background: var(--bg3);
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .result-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.45rem 0;
        border-bottom: 1px solid var(--border);
        font-size: 0.85rem;
    }

    .result-info-row:last-child { border-bottom: none; }
    .result-info-row .rl { color: var(--muted); }
    .result-info-row .rv { font-weight: 600; }
    .result-info-row .rv.total { color: var(--lime); font-weight: 800; }

    .btn-lanjut {
        display: block;
        text-align: center;
        background: var(--lime);
        color: #000;
        padding: 0.8rem;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.875rem;
        text-decoration: none;
        margin-top: 1rem;
        transition: 0.2s;
    }

    .btn-lanjut:hover { background: var(--lime-dark); }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="layanan-page">
        <div style="text-align:center;margin-bottom:2rem;">
            <h1 style="font-size:clamp(1.6rem,3vw,2.2rem);font-weight:800;">Layanan <span style="color:var(--lime);">Kami</span></h1>
            <p style="color:var(--muted);font-size:0.875rem;margin-top:0.3rem;">Pilih layanan yang kamu butuhkan</p>
        </div>

     

        <div class="layanan-container">
               <a href="/#layanan" style="display:inline-flex;align-items:center;gap:0.4rem;color:var(--muted);text-decoration:none;font-size:0.85rem;font-weight:500;margin-bottom:1.75rem;transition:color 0.2s;align-self:flex-start;width:100%;max-width:520px;">
            ← Kembali ke layanan
        </a>
            <div class="cek-card">
                <h2>Cek Pendaftaran</h2>
                <p class="subtitle">Masukkan kode invoice atau nomor WhatsApp</p>

                @if(session('error'))
                    <div class="alert alert-error" style="margin-bottom:1.25rem;">{{ session('error') }}</div>
                @endif

                <form method="GET" action="/cek-status">
                    <div class="search-row">
                        <div class="search-wrap">
                            <span class="search-icon">🔍</span>
                            <input type="text" name="search" class="search-input"
                                placeholder="INV-XXXXXX atau No WA"
                                value="{{ request('search') }}">
                        </div>
                        <button type="submit" class="btn-cari">Cari</button>
                    </div>
                </form>

                {{-- Ini tidak akan muncul karena controller redirect otomatis --}}
                {{-- Tapi jika ingin tampilkan hasil di sini: --}}
            </div>
        </div>
    </div>
</div>
@endsection