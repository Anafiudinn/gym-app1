@extends('layouts.landing')

@push('styles')
<style>
    .daftar-page {
        min-height: calc(100vh - 60px);
        padding: 3rem 1rem 4rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--muted);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 1.75rem;
        transition: color 0.2s;
        align-self: flex-start;
        max-width: 600px;
        width: 100%;
    }

    .back-link:hover {
        color: var(--lime);
    }

    .daftar-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 20px;
        padding: 2.25rem;
        width: 100%;
        max-width: 600px;
    }

    .daftar-card h2 {
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }

    .daftar-card .subtitle {
        font-size: 0.85rem;
        color: var(--muted);
        margin-bottom: 2rem;
    }

    .paket-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.6rem;
    }

    .paket-option {
        position: relative;
    }

    .paket-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
    }

    .paket-label {
        display: block;
        background: var(--bg3);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        padding: 0.85rem 1rem;
        cursor: pointer;
        transition: 0.2s;
    }

    .paket-label:hover {
        border-color: var(--lime);
    }

    .paket-option input[type="radio"]:checked+.paket-label {
        border-color: var(--lime);
        background: rgba(170, 255, 0, 0.07);
    }

    .paket-name {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text);
    }

    .paket-price {
        font-size: 0.78rem;
        color: var(--lime);
        font-weight: 600;
        margin-top: 0.1rem;
    }

    .gender-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.6rem;
    }

    .gender-option {
        position: relative;
    }

    .gender-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
    }

    .gender-label {
        display: block;
        background: var(--bg3);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.7rem;
        text-align: center;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--muted);
        transition: 0.2s;
    }

    .gender-label:hover {
        border-color: var(--lime);
        color: var(--lime);
    }

    .gender-option input[type="radio"]:checked+.gender-label {
        border-color: var(--lime);
        color: var(--lime);
        background: rgba(170, 255, 0, 0.07);
    }

    .form-control-plain {
        width: 100%;
        background: var(--bg3);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        color: var(--text);
        font-family: 'Outfit', sans-serif;
        font-size: 0.9rem;
        transition: border-color 0.2s;
        outline: none;
    }

    .form-control-plain:focus {
        border-color: var(--lime);
    }

    .form-control-plain::placeholder {
        color: #444;
    }

    .error-msg {
        color: var(--danger);
        font-size: 0.78rem;
        margin-top: 0.3rem;
    }

    .btn-submit {
        width: 100%;
        background: var(--lime);
        color: #000;
        border: none;
        padding: 0.9rem;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.95rem;
        cursor: pointer;
        font-family: 'Outfit', sans-serif;
        transition: background 0.2s, transform 0.1s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .btn-submit:hover {
        background: var(--lime-dark);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')

<div class="daftar-page">
    <div style="width:100%;max-width:600px;"> 
        <div style="text-align:center;margin-bottom:2rem;">
            <h1 style="font-size:clamp(1.6rem,3vw,2.2rem);font-weight:800;">Layanan <span style="color:var(--lime);">Kami</span></h1>
            <p style="color:var(--muted);font-size:0.875rem;margin-top:0.3rem;">Pilih layanan yang kamu butuhkan</p>
        </div>

        <div class="steps" style="margin-bottom:2rem;">
            ...
        </div>

    

        <!-- Step Indicator -->
        <div class="steps" style="margin-bottom:2rem;">
            <div class="step-circle active">1</div>
            <div class="step-line"></div>
            <div class="step-circle">2</div>
            <div class="step-line"></div>
            <div class="step-circle">3</div>
        </div>
          {{-- BACK LINK --}}
       
        <a href="/" class="back-link">← Kembali ke beranda</a>

        <div class="daftar-card">
            <h2>Data Diri</h2>
            <p class="subtitle">Lengkapi data diri kamu untuk mendaftar</p>

            {{-- Alert Error Session --}}
            @if(session('error'))
            <div style="background: rgba(255, 68, 68, 0.1); border: 1.5px solid #ff4444; color: #ff4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.2rem;">⚠️</span>
                <div>
                    <strong style="display: block; font-size: 0.9rem;">Pendaftaran Gagal</strong>
                    <span style="font-size: 0.8rem; opacity: 0.9;">{{ session('error') }}</span>
                </div>
            </div>
            @endif

            {{-- Alert Error Validasi (Input Kosong/Salah) --}}
            @if($errors->any())
            <div style="background: rgba(255, 165, 0, 0.1); border: 1.5px solid #ffa500; color: #ffa500; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <strong style="display: block; font-size: 0.9rem; margin-bottom: 0.25rem;">Periksa kembali inputan Anda:</strong>
                <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.8rem;">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form method="POST" action="/daftar">
                @csrf

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <div class="input-wrap">
                        <span class="icon">👤</span>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('nama') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>No. WhatsApp</label>
                    <div class="input-wrap">
                        <span class="icon">📱</span>
                        <input type="text" name="no_wa" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('no_wa') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Gender</label>
                    <div class="gender-group">
                        <div class="gender-option">
                            <input type="radio" name="jenis_kelamin" id="laki" value="L" {{ old('jenis_kelamin') === 'L' ? 'checked' : '' }} required>
                            <label for="laki" class="gender-label">Laki-laki</label>
                        </div>
                        <div class="gender-option">
                            <input type="radio" name="jenis_kelamin" id="perempuan" value="P" {{ old('jenis_kelamin') === 'P' ? 'checked' : '' }}>
                            <label for="perempuan" class="gender-label">Perempuan</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Pilih Paket</label>
                    <div class="paket-grid">
                        @foreach($paket as $p)
                        <div class="paket-option">
                            <input type="radio" name="paket_id" id="paket_{{ $p->id }}" value="{{ $p->id }}"
                                {{ (old('paket_id') == $p->id || $selectedPaketId == $p->id) ? 'checked' : '' }} required>
                            <label for="paket_{{ $p->id }}" class="paket-label">
                                <div class="paket-name">{{ $p->nama_paket }}</div>
                                <div class="paket-price">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Lanjut ke Pembayaran →
                </button>
            </form>
        </div>
    </div>
</div>
@endsection