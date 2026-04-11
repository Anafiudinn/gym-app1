<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JefryGym - Transform Your Body & Mind</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --lime: #AAFF00;
            --lime-dark: #88CC00;
            --bg: #0a0a0a;
            --bg2: #111111;
            --bg3: #1a1a1a;
            --card: #161616;
            --border: #222;
            --text: #ffffff;
            --muted: #888;
            --danger: #ff4444;
            --warning: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        a, button { -webkit-tap-highlight-color: transparent; }

        /* =====================
           NAVBAR
        ===================== */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            height: 60px;
            background: rgba(10,10,10,0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text);
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 1px;
            flex-shrink: 0;
        }

        .nav-logo-icon {
            width: 28px; height: 28px;
            background: var(--lime);
            border-radius: 6px;
            display: grid;
            place-items: center;
        }

        .nav-logo-icon svg { width: 16px; height: 16px; }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--muted);
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--text); }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Hamburger Button */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 42px;
            height: 42px;
            cursor: pointer;
            background: none;
            border: 1.5px solid var(--border);
            padding: 8px;
            border-radius: 10px;
            transition: border-color 0.2s;
        }

        .hamburger:hover { border-color: var(--lime); }

        .hamburger span {
            display: block;
            width: 100%;
            height: 2px;
            background: var(--text);
            border-radius: 2px;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            transform-origin: center;
        }

        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* Mobile Drawer */
        .mobile-menu {
            position: fixed;
            top: 60px; left: 0; right: 0;
            background: rgba(10,10,10,0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            z-index: 199;
            padding: 0.5rem 1.25rem 1.5rem;
            flex-direction: column;
            pointer-events: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            display: flex;
        }

        .mobile-menu.open {
            pointer-events: all;
            opacity: 1;
            transform: translateY(0);
        }

        .mobile-menu a {
            text-decoration: none;
            color: var(--text);
            font-size: 1rem;
            font-weight: 500;
            padding: 0.9rem 0;
            border-bottom: 1px solid var(--border);
            display: block;
            transition: color 0.2s, padding-left 0.2s;
        }

        .mobile-menu a:hover { color: var(--lime); padding-left: 4px; }

        .mobile-menu .mobile-cta {
            margin-top: 1rem;
            background: var(--lime);
            color: #000 !important;
            text-align: center;
            border-radius: 12px;
            font-weight: 800 !important;
            padding: 0.9rem !important;
            border-bottom: none !important;
        }

        .mobile-menu .mobile-cta:hover {
            background: var(--lime-dark);
            padding-left: 0 !important;
        }

        /* =====================
           BUTTONS
        ===================== */
        .btn-primary {
            background: var(--lime);
            color: #000;
            border: none;
            padding: 0.55rem 1.25rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s, transform 0.15s;
            font-family: 'Outfit', sans-serif;
            white-space: nowrap;
        }

        .btn-primary:hover { background: var(--lime-dark); transform: translateY(-1px); }
        .btn-primary:active { transform: scale(0.96); }

        .btn-secondary {
            background: transparent;
            color: var(--text);
            border: 1.5px solid #444;
            padding: 0.55rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: border-color 0.2s, color 0.2s;
            font-family: 'Outfit', sans-serif;
        }

        .btn-secondary:hover { border-color: var(--lime); color: var(--lime); }

        /* =====================
           ALERTS
        ===================== */
        .alert {
            padding: 0.875rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(170,255,0,0.1);
            border: 1px solid rgba(170,255,0,0.3);
            color: var(--lime);
        }

        .alert-error {
            background: rgba(255,68,68,0.1);
            border: 1px solid rgba(255,68,68,0.3);
            color: var(--danger);
        }

        /* =====================
           SECTION TITLE
        ===================== */
        .section-title {
            text-align: center;
            margin-bottom: 2.5rem;
            padding: 0 1rem;
        }

        .section-title h2 {
            font-size: clamp(1.75rem, 6vw, 3rem);
            font-weight: 700;
        }

        .section-title h2 span { color: var(--lime); }

        .section-title p {
            color: var(--muted);
            margin-top: 0.4rem;
            font-size: 0.875rem;
        }

        /* =====================
           STEP INDICATOR
        ===================== */
        .steps {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.75rem;
        }

        .step-circle {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 0.875rem;
            background: var(--bg3);
            color: var(--muted);
            border: 2px solid var(--border);
            flex-shrink: 0;
        }

        .step-circle.active { background: var(--lime); color: #000; border-color: var(--lime); }
        .step-circle.done { background: rgba(170,255,0,0.15); color: var(--lime); border-color: var(--lime); }

        .step-line { width: 48px; height: 2px; background: var(--border); }
        .step-line.active { background: var(--lime); }

        /* =====================
           FORMS
        ===================== */
        .form-group { margin-bottom: 1.25rem; }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            background: var(--bg3);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            font-size: 16px; /* prevents iOS auto-zoom */
            transition: border-color 0.2s;
            outline: none;
            -webkit-appearance: none;
        }

        .form-control:focus { border-color: var(--lime); }
        .form-control::placeholder { color: #444; }

        .input-wrap { position: relative; }

        .input-wrap .icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 1rem;
            pointer-events: none;
        }

        /* =====================
           PAGE WRAPPER
        ===================== */
        .page-wrapper {
            min-height: 100vh;
            padding-top: 60px;
        }

        .layanan-page {
            padding: 2.5rem 1rem 4rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* =====================
           FOOTER
        ===================== */
        footer {
            border-top: 1px solid var(--border);
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--muted);
            font-size: 0.78rem;
            flex-wrap: wrap;
            gap: 0.75rem;
            background: var(--bg2);
        }

        /* =====================
           RESPONSIVE
        ===================== */
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .nav-right .btn-primary { display: none; }
            .hamburger { display: flex; }
        }

        @media (min-width: 769px) {
            nav { padding: 0 2.5rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav>
    <a href="/" class="nav-logo">
        <div class="nav-logo-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 4v16M18 4v16M3 8h3M18 8h3M3 16h3M18 16h3M6 12h12" stroke="#000" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        JEFRYGYM
    </a>

    <ul class="nav-links">
        <li><a href="/#home">Home</a></li>
        <li><a href="/#harga">Harga</a></li>
        <li><a href="/#keramaian">Keramaian</a></li>
        <li><a href="/#layanan">Layanan</a></li>
        <li><a href="/#galeri">Galeri</a></li>
        <li><a href="/#lokasi">Lokasi</a></li>
        <li><a href="/#kontak">Kontak</a></li>
    </ul>

    <div class="nav-right">
        <a href="/daftar" class="btn-primary">Daftar Sekarang</a>
        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>

<div class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <a href="/#home" class="mobile-link">Home</a>
    <a href="/#harga" class="mobile-link">Harga</a>
    <a href="/#keramaian" class="mobile-link">Keramaian</a>
    <a href="/#layanan" class="mobile-link">Layanan</a>
    <a href="/#galeri" class="mobile-link">Galeri</a>
    <a href="/#lokasi" class="mobile-link">Lokasi</a>
    <a href="/#kontak" class="mobile-link">Kontak</a>
    <a href="/daftar" class="mobile-cta">Daftar Sekarang →</a>
</div>

@yield('content')

<script>
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    let menuOpen = false;

    function toggleMenu(force) {
        menuOpen = force !== undefined ? force : !menuOpen;
        hamburger.classList.toggle('open', menuOpen);
        mobileMenu.classList.toggle('open', menuOpen);
        mobileMenu.setAttribute('aria-hidden', !menuOpen);
        document.body.style.overflow = menuOpen ? 'hidden' : '';
    }

    hamburger.addEventListener('click', () => toggleMenu());

    document.querySelectorAll('.mobile-link, .mobile-cta').forEach(link => {
        link.addEventListener('click', () => toggleMenu(false));
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (menuOpen && !mobileMenu.contains(e.target) && !hamburger.contains(e.target)) {
            toggleMenu(false);
        }
    });
</script>
</body>
</html>