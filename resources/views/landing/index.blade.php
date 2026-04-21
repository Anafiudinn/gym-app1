@extends('layouts.landing')

@push('styles')
<style>
    /* ===================== HERO ===================== */
    #home {
        position: relative;
        min-height: 100svh;
        display: flex;
        align-items: center;
        overflow: hidden;
    }

    .hero-bg {
        position: absolute;
        inset: 0;
        /* Optimized: smaller w=1200, webp format, lower quality */
        background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=75&fm=webp');
        background-size: cover;
        background-position: center;
        filter: brightness(0.22);
        will-change: auto;
    }

    .hero-inner {
        position: relative;
        z-index: 2;
        width: 100%;
        display: flex;
        flex-direction: column;
        min-height: 100svh;
    }

    .hero-content {
        flex: 1;
        display: flex;
        align-items: center;
        padding: 5rem 1.5rem 2rem;
    }

    .hero-text { max-width: 680px; }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 999px;
        padding: 0.35rem 1rem;
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 1.25rem;
    }

    .hero-title {
        font-size: clamp(2.8rem, 10vw, 5.5rem);
        font-weight: 800;
        line-height: 1.0;
        margin-bottom: 1rem;
        letter-spacing: -1px;
    }

    .hero-title span { color: var(--lime); }

    .hero-desc {
        color: #bbb;
        font-size: clamp(0.875rem, 3vw, 1rem);
        line-height: 1.7;
        max-width: 480px;
        margin-bottom: 1.75rem;
    }

    .hero-cta {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .hero-cta a {
        padding: 0.8rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 12px;
        flex: 1;
        text-align: center;
        min-width: 140px;
    }

    /* ===================== HARGA ===================== */
    #harga {
        padding: 4rem 1rem;
        background: var(--bg2);
    }

    .pricing-scroll-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        padding-bottom: 0.5rem;
        margin: 0 -1rem;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .pricing-scroll-wrap::-webkit-scrollbar { display: none; }

    .pricing-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(230px, 1fr));
        gap: 1rem;
        width: max-content;
        min-width: 100%;
    }

    .pricing-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        scroll-snap-align: start;
        transition: border-color 0.2s, transform 0.2s;
    }

    .pricing-card:hover { border-color: var(--lime); transform: translateY(-3px); }
    .pricing-card.popular { border-color: var(--lime); background: var(--lime); }

    .popular-badge {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--bg);
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 0.6rem;
    }

    .pkg-name { font-size: 1rem; font-weight: 700; margin-bottom: 0.4rem; }
    .pkg-price { font-size: 1.8rem; font-weight: 800; color: var(--lime); line-height: 1; }
    .pricing-card.popular .pkg-price { color: var(--bg); }
    .pkg-per { font-size: 0.78rem; color: var(--muted); margin-bottom: 1rem; }
    .pricing-card.popular .pkg-per { color: rgba(0,0,0,0.55); }

    .pkg-features { list-style: none; margin-bottom: 1.25rem; }

    .pkg-feature {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.82rem;
        color: var(--muted);
        padding: 0.25rem 0;
    }

    .pkg-feature::before { content: '✓'; color: var(--lime); font-weight: 700; }
    .pricing-card.popular .pkg-feature::before { color: var(--bg); }
    .pricing-card.popular .pkg-feature { color: rgba(0,0,0,0.7); }
    .pricing-card.popular .pkg-name { color: var(--bg); }

    .btn-pilih {
        display: block;
        width: 100%;
        text-align: center;
        padding: 0.7rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        transition: 0.2s;
        border: none;
        font-family: 'Outfit', sans-serif;
    }

    .pricing-card:not(.popular) .btn-pilih { background: var(--lime); color: #000; }
    .pricing-card.popular .btn-pilih { background: var(--bg); color: var(--lime); }
    .btn-pilih:hover { opacity: 0.85; }

    .scroll-dots {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-top: 1.25rem;
    }

    .scroll-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--border);
        transition: background 0.2s, width 0.2s;
    }

    .scroll-dot.active { background: var(--lime); width: 18px; border-radius: 3px; }

    /* ===================== KERAMAIAN ===================== */
    #keramaian { padding: 4rem 1rem; }

    .keramaian-wrap { max-width: 800px; margin: 0 auto; }

    .keramaian-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
    }

    .keramaian-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .status-sepi  { background: rgba(170,255,0,0.1); color: var(--lime); }
    .status-ramai { background: rgba(245,158,11,0.1); color: var(--warning); }
    .status-penuh { background: rgba(255,68,68,0.1); color: var(--danger); }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    .status-sepi .status-dot  { background: var(--lime); }
    .status-ramai .status-dot { background: var(--warning); }
    .status-penuh .status-dot { background: var(--danger); }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.4; transform: scale(0.8); }
    }

    .crowd-count { font-size: clamp(1.5rem, 6vw, 1.75rem); font-weight: 800; color: var(--lime); }

    .crowd-bar-wrap {
        background: var(--bg3);
        border-radius: 999px;
        height: 10px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .crowd-bar { height: 100%; border-radius: 999px; background: var(--lime); transition: width 1s ease; }
    .crowd-bar.ramai { background: var(--warning); }
    .crowd-bar.penuh { background: var(--danger); }

    .crowd-meta { display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--muted); }

    .time-label { font-size: 0.78rem; color: var(--muted); margin-top: 1.25rem; margin-bottom: 0.6rem; }

    .time-grid {
        display: flex;
        gap: 0.3rem;
        overflow-x: auto;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }

    .time-grid::-webkit-scrollbar { display: none; }

    .time-slot {
        background: var(--bg3);
        border-radius: 6px;
        padding: 0.4rem 0.5rem;
        font-size: 0.68rem;
        color: var(--muted);
        flex-shrink: 0;
        min-width: 36px;
        text-align: center;
    }

    /* ===================== LAYANAN ===================== */
    #layanan { padding: 4rem 1rem; background: var(--bg2); }

    .layanan-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        max-width: 860px;
        margin: 0 auto;
    }

    .layanan-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem 1rem;
        text-align: center;
        text-decoration: none;
        color: var(--text);
        transition: border-color 0.2s, transform 0.2s;
        display: block;
    }

    .layanan-card:hover { border-color: var(--lime); transform: translateY(-3px); }
    .layanan-card:active { transform: scale(0.97); }

    .layanan-icon {
        width: 48px;
        height: 48px;
        background: rgba(170,255,0,0.08);
        border: 1.5px solid rgba(170,255,0,0.2);
        border-radius: 12px;
        display: grid;
        place-items: center;
        margin: 0 auto 0.85rem;
        color: var(--lime);
    }

    .layanan-card h3 { font-size: 0.875rem; font-weight: 700; margin-bottom: 0.3rem; }
    .layanan-card p { font-size: 0.75rem; color: var(--muted); }

    /* ===================== GALERI ===================== */
    #galeri { padding: 4rem 1rem; }

    .galeri-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        max-width: 1100px;
        margin: 0 auto;
    }

    .galeri-item {
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 4/3;
        background: var(--bg3);
    }

    .galeri-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s;
        display: block;
    }

    .galeri-item:hover img { transform: scale(1.05); }

    /* ===================== LOKASI ===================== */
    #lokasi { padding: 4rem 1rem; background: var(--bg2); }

    .lokasi-wrap {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .lokasi-info {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
    }

    .info-row { display: flex; gap: 0.875rem; margin-bottom: 1.25rem; }
    .info-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 2px; }
    .info-label { font-size: 0.75rem; color: var(--muted); margin-bottom: 0.2rem; }
    .info-value { font-size: 0.875rem; font-weight: 500; line-height: 1.5; }

    .map-container {
        border-radius: 16px;
        overflow: hidden;
        border: 1.5px solid var(--border);
        height: 240px;
        position: relative;
        background: var(--bg3);
        cursor: pointer;
    }

    /* Lazy map placeholder */
    .map-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        color: var(--muted);
        font-size: 0.85rem;
        font-weight: 500;
        user-select: none;
    }

    .map-placeholder .map-icon { font-size: 2rem; }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
        filter: invert(1) hue-rotate(180deg);
        display: none;
    }

    .map-container.loaded iframe { display: block; }
    .map-container.loaded .map-placeholder { display: none; }

    /* ===================== KONTAK ===================== */
    #kontak { padding: 4rem 1rem; }

    .kontak-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        max-width: 860px;
        margin: 0 auto;
    }

    .kontak-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem 1rem;
        text-align: center;
        text-decoration: none;
        color: var(--text);
        transition: border-color 0.2s, transform 0.2s;
        display: block;
    }

    .kontak-card:hover { border-color: var(--lime); transform: translateY(-2px); }
    .kontak-card:active { transform: scale(0.97); }
    .kontak-icon { font-size: 1.6rem; margin-bottom: 0.6rem; }
    .kontak-label { font-weight: 700; font-size: 0.875rem; margin-bottom: 0.2rem; }
    .kontak-val { font-size: 0.78rem; color: var(--muted); }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 640px) {
        .hero-content { padding: 5rem 1.25rem 1.5rem; }
        .hero-title { letter-spacing: -0.5px; }
        .hero-cta a { min-width: 0; }
        .pricing-grid { grid-template-columns: repeat(4, 260px); }
        .layanan-grid { grid-template-columns: 1fr; max-width: 400px; }
        .layanan-card { display: flex; align-items: center; text-align: left; gap: 1rem; padding: 1.1rem 1.25rem; }
        .layanan-icon { margin: 0; flex-shrink: 0; }
        .layanan-card h3 { font-size: 0.925rem; }
        .galeri-grid { grid-template-columns: 1fr 1fr; }
        .lokasi-wrap { grid-template-columns: 1fr; }
        .map-container { height: 200px; }
        .kontak-grid { grid-template-columns: 1fr; }
        .kontak-card { display: flex; align-items: center; gap: 1rem; text-align: left; padding: 1.1rem 1.25rem; }
        .kontak-icon { margin: 0; flex-shrink: 0; font-size: 1.4rem; }
    }

    @media (max-width: 480px) {
        .stat-item { padding: 1rem 0.75rem; }
        .hero-cta { gap: 0.6rem; }
    }

    @media (min-width: 641px) and (max-width: 900px) {
        .pricing-grid { grid-template-columns: repeat(4, minmax(200px, 1fr)); }
        .layanan-grid { grid-template-columns: repeat(3, 1fr); }
        .lokasi-wrap { grid-template-columns: 1fr; }
        .kontak-grid { grid-template-columns: repeat(3, 1fr); }
        .galeri-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (min-width: 901px) {
        .pricing-scroll-wrap { overflow: visible; margin: 0; padding: 0; }
        .pricing-grid { grid-template-columns: repeat(4, 1fr); width: 100%; max-width: 1100px; margin: 0 auto; }
    }
</style>
@endpush

@section('content')

<!-- ========== HERO ========== -->
<section id="home">
    {{-- Hero bg via inline style untuk LCP — fetchpriority sudah di <head> --}}
    <div class="hero-bg" role="img" aria-label="Gym interior background"></div>
    <div class="hero-inner">
        <div class="hero-content">
            <div class="hero-text">
                <div class="badge">🔥 Promo Membership Spesial</div>
                <h1 class="hero-title">
                    Transform Your<br>
                    <span>Body & Mind</span>
                </h1>
                <p class="hero-desc">
                    Bergabunglah dengan komunitas fitness terbaik. Fasilitas lengkap, trainer profesional, dan lingkungan yang mendukung perjalanan kebugaran Anda.
                </p>
                <div class="hero-cta">
                    <a href="/#layanan" class="btn-primary">Mulai Sekarang →</a>
                    <a href="#harga" class="btn-secondary">Lihat Harga</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== HARGA ========== -->
<section id="harga">
    <div class="section-title">
        <h2>Daftar <span>Harga</span></h2>
        <p>Pilih paket yang sesuai dengan kebutuhan dan budget kamu</p>
    </div>

    <div class="pricing-scroll-wrap" id="pricingScroll">
        <div class="pricing-grid" id="pricingGrid">
            @foreach($paket as $p)
            @php
                $namaPaketClean = trim(strtolower($p->nama_paket));
                $isPopular = str_contains($namaPaketClean, '1 bulan');
                $perLabel = match(true) {
                    str_contains($namaPaketClean, 'harian')  => '/harian',
                    str_contains($namaPaketClean, '1 bulan') => '/1 bulan',
                    str_contains($namaPaketClean, '3 bulan') => '/3 bulan',
                    str_contains($namaPaketClean, '1 tahun') => '/1 tahun',
                    default => ''
                };
                $features = match(true) {
                    str_contains($namaPaketClean, 'harian')  => ['Akses semua alat', 'Untuk tamu harian non-member', 'Daily pass 1x masuk'],
                    str_contains($namaPaketClean, '1 bulan') => ['Akses semua alat', 'Locker room', 'Free air minum', 'Member card'],
                    str_contains($namaPaketClean, '3 bulan') => ['Akses semua alat', 'Locker room', 'Free air minum', 'Member card', 'Free 1 Kaos'],
                    str_contains($namaPaketClean, '1 tahun') => ['Akses semua alat', 'Locker room', 'Free air minum', 'Member card', 'Free 1 Kaos', 'Priority booking'],
                    default => ['Akses semua alat']
                };
            @endphp
            <div class="pricing-card {{ $isPopular ? 'popular' : '' }}">
                @if($isPopular)
                <div class="popular-badge">☆ POPULER</div>
                @endif
                <div class="pkg-name">{{ $p->nama_paket }}</div>
                <div class="pkg-price">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                <div class="pkg-per">{{ $perLabel }}</div>
                <ul class="pkg-features">
                    @foreach($features as $f)
                    <li class="pkg-feature">{{ $f }}</li>
                    @endforeach
                </ul>
                <a href="/daftar?paket_id={{ $p->id }}" class="btn-pilih">Pilih Paket</a>
            </div>
            @endforeach
        </div>
    </div>

    <div class="scroll-dots" id="scrollDots" style="display:none;">
        @foreach($paket as $i => $p)
        <div class="scroll-dot {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"></div>
        @endforeach
    </div>
</section>

<!-- ========== KERAMAIAN ========== -->
<section id="keramaian">
    <div class="section-title">
        <h2>Keramaian <span>Gym</span></h2>
        <p>Pantau keramaian gym secara real-time</p>
    </div>

    <div class="keramaian-wrap">
        <div class="keramaian-card">
            <div class="keramaian-top">
                <div>
                    <div style="font-size:0.75rem;color:var(--muted);margin-bottom:0.4rem;">Status Saat Ini</div>
                    <div class="status-badge {{ strtolower($statusKeramaian) === 'sepi' ? 'status-sepi' : (strtolower($statusKeramaian) === 'ramai' ? 'status-ramai' : 'status-penuh') }}">
                        <span class="status-dot"></span>
                        {{ $statusKeramaian }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.75rem;color:var(--muted);">Orang di Gym</div>
                    <div class="crowd-count">{{ $totalKeramaian }}</div>
                </div>
            </div>

            @php
                $pct = min(($totalKeramaian / 60) * 100, 100);
                $barClass = $totalKeramaian > 50 ? 'penuh' : ($totalKeramaian > 20 ? 'ramai' : '');
            @endphp
            <div class="crowd-bar-wrap">
                <div class="crowd-bar {{ $barClass }}" style="width:{{ $pct }}%"></div>
            </div>
            <div class="crowd-meta">
                <span>0</span>
                <span>Kapasitas: 60 orang</span>
            </div>

            <div class="time-label">Perkiraan Jam Ramai</div>
            <div class="time-grid">
                @foreach(range(6, 22) as $hour)
                <div class="time-slot">{{ sprintf('%02d', $hour) }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- ========== LAYANAN ========== -->
<section id="layanan">
    <div class="section-title">
        <h2>Layanan <span>Kami</span></h2>
        <p>Pilih layanan yang kamu butuhkan</p>
    </div>

    <div class="layanan-grid">
        <a href="/daftar" class="layanan-card">
            <div class="layanan-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
            </div>
            <div>
                <h3>Daftar Membership</h3>
                <p>Daftarkan diri kamu untuk menjadi member gym</p>
            </div>
        </a>

        <a href="/cek-membership" class="layanan-card">
            <div class="layanan-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
            </div>
            <div>
                <h3>Cek Membership</h3>
                <p>Cek status dan detail membership kamu</p>
            </div>
        </a>

        <a href="/cek-status" class="layanan-card">
            <div class="layanan-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <div>
                <h3>Cek Pendaftaran</h3>
                <p>Lacak status pendaftaran kamu</p>
            </div>
        </a>
    </div>
</section>

<!-- ========== GALERI ========== -->
<section id="galeri">
    <div class="section-title">
        <h2>Info <span>Mitra</span></h2>
        <p>Fasilitas lengkap untuk mendukung latihan kamu</p>
    </div>

    {{--
        OPTIMIZED:
        - Explicit width/height to prevent CLS
        - Smaller image sizes via w= param
        - webp format for better compression
        - loading="lazy" only for below-fold images
    --}}
    <div class="galeri-grid">
        <div class="galeri-item">
            <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=480&q=75&fm=webp"
                 alt="Gym Equipment" width="480" height="360" loading="lazy" decoding="async">
        </div>
        <div class="galeri-item">
            <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=480&q=75&fm=webp"
                 alt="Group Class" width="480" height="360" loading="lazy" decoding="async">
        </div>
        <div class="galeri-item">
            <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=480&q=75&fm=webp"
                 alt="Locker Room" width="480" height="360" loading="lazy" decoding="async">
        </div>
        <div class="galeri-item">
            <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=480&q=75&fm=webp"
                 alt="Gym Training" width="480" height="360" loading="lazy" decoding="async">
        </div>
    </div>
</section>

<!-- ========== LOKASI ========== -->
<section id="lokasi">
    <div class="section-title">
        <h2>Lokasi <span>Kami</span></h2>
    </div>

    <div class="lokasi-wrap">
        <div class="lokasi-info">
            <div class="info-row">
                <div class="info-icon">📍</div>
                <div>
                    <div class="info-label">Alamat</div>
                    <div class="info-value">{{ \App\Models\Setting::getValue('alamat_gym', 'Belum diisi') }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-icon">🕐</div>
                <div>
                    <div class="info-label">Jam Operasional</div>
                    <div class="info-value">
                        {!! nl2br(e(\App\Models\Setting::getValue('jam_operasional', "Senin - Jumat: 08:00 - 21:00"))) !!}
                    </div>
                </div>
            </div>
            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(\App\Models\Setting::getValue('alamat_gym')) }}"
               target="_blank" rel="noopener" class="btn-primary"
               style="display:inline-flex;align-items:center;gap:0.4rem;">
                ✈ Buka di Google Maps
            </a>
        </div>

        {{--
            OPTIMIZED: Google Maps lazy load — iframe hanya diload saat diklik.
            Ini menghemat ~500ms DNS + connection time untuk semua visitor.
        --}}
        <div class="map-container" id="mapContainer"
             data-src="{{ \App\Models\Setting::getValue('google_maps_url', '') }}"
             onclick="loadMap(this)" role="button" aria-label="Klik untuk memuat peta">
            <div class="map-placeholder">
                <span class="map-icon">🗺️</span>
                <span>Klik untuk memuat peta</span>
            </div>
            <iframe id="mapIframe" src="" allowfullscreen loading="lazy" title="Lokasi Gym"></iframe>
        </div>
    </div>
</section>

<!-- ========== KONTAK ========== -->
<section id="kontak">
    <div class="section-title">
        <h2>Hubungi <span>Kami</span></h2>
        <p>Ada pertanyaan? Jangan ragu untuk menghubungi kami</p>
    </div>

    <div class="kontak-grid">
        <a href="https://instagram.com/{{ \App\Models\Setting::getValue('instagram') }}" target="_blank" rel="noopener" class="kontak-card">
            <div class="kontak-icon">📸</div>
            <div>
                <div class="kontak-label">Instagram</div>
                <div class="kontak-val">{{ \App\Models\Setting::getValue('instagram', '@gym') }}</div>
            </div>
        </a>
        <a href="https://wa.me/{{ '62' . ltrim(preg_replace('/[^0-9]/', '', \App\Models\Setting::getValue('no_telp')), '0') }}"
           target="_blank" rel="noopener" class="kontak-card">
            <div class="kontak-icon">💬</div>
            <div>
                <div class="kontak-label">WhatsApp</div>
                <div class="kontak-val">{{ \App\Models\Setting::getValue('no_telp', '0812-xxxx') }}</div>
            </div>
        </a>
        <a href="mailto:{{ \App\Models\User::where('role', 'admin')->first()->email ?? 'info@gym.com' }}" class="kontak-card">
            <div class="kontak-icon">✉</div>
            <div>
                <div class="kontak-label">Email</div>
                <div class="kontak-val">{{ \App\Models\User::where('role', 'admin')->first()->email ?? 'info@gym.com' }}</div>
            </div>
        </a>
    </div>
</section>

<footer>
    <div style="display:flex;align-items:center;gap:0.5rem;">
        <div style="width:22px;height:22px;background:var(--lime);border-radius:5px;display:grid;place-items:center;">
            <svg viewBox="0 0 24 24" fill="none" width="12" height="12">
                <path d="M6 4v16M18 4v16M3 8h3M18 8h3M3 16h3M18 16h3M6 12h12" stroke="#000" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <span style="font-weight:700;letter-spacing:1px;">{{ \App\Models\Setting::getValue('nama_gym') }}</span>
    </div>
    <span>© {{ date('Y') }} {{ \App\Models\Setting::getValue('nama_gym') }}. All rights reserved.</span>
</footer>

<script>
/* ── Lazy load Google Maps on click ── */
function loadMap(container) {
    const src = container.dataset.src;
    if (!src) return;
    const iframe = document.getElementById('mapIframe');
    iframe.src = src;
    container.classList.add('loaded');
}

/* ── Pricing scroll dots indicator ── */
const pricingScroll = document.getElementById('pricingScroll');
const scrollDots    = document.getElementById('scrollDots');
const dots          = document.querySelectorAll('.scroll-dot');

function checkMobile() {
    scrollDots.style.display = window.innerWidth <= 900 ? 'flex' : 'none';
}

checkMobile();
window.addEventListener('resize', checkMobile, { passive: true });

if (pricingScroll) {
    pricingScroll.addEventListener('scroll', () => {
        const maxScroll = pricingScroll.scrollWidth - pricingScroll.clientWidth;
        const ratio     = pricingScroll.scrollLeft / maxScroll;
        const activeIdx = Math.round(ratio * (dots.length - 1));
        dots.forEach((d, i) => d.classList.toggle('active', i === activeIdx));
    }, { passive: true });
}
</script>
@endsection