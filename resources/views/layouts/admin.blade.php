<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — JefryGym Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --lime: #AAFF00;
            --lime-dim: rgba(170,255,0,0.1);
            --lime-border: rgba(170,255,0,0.2);
            --bg: #080808;
            --sidebar: #0e0e0e;
            --surface: #141414;
            --surface2: #1a1a1a;
            --border: #242424;
            --text: #f0f0f0;
            --muted: #666;
            --muted2: #444;
            --danger: #ff4444;
            --danger-dim: rgba(255,68,68,0.1);
            --warning: #f59e0b;
            --warning-dim: rgba(245,158,11,0.1);
            --info: #38bdf8;
            --info-dim: rgba(56,189,248,0.1);
            --success: #4ade80;
            --success-dim: rgba(74,222,128,0.1);
            --sidebar-w: 240px;
            --topbar-h: 56px;
            --radius: 12px;
            --radius-sm: 8px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        a { -webkit-tap-highlight-color: transparent; }
        button { -webkit-tap-highlight-color: transparent; cursor: pointer; font-family: 'Outfit', sans-serif; }

        /* =====================
           SIDEBAR
        ===================== */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform 0.3s cubic-bezier(0.23,1,0.32,1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0 1.25rem;
            height: var(--topbar-h);
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            flex-shrink: 0;
        }

        .logo-icon {
            width: 30px; height: 30px;
            background: var(--lime);
            border-radius: 8px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .logo-icon svg { width: 17px; height: 17px; }

        .logo-text {
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: 1.5px;
            color: var(--text);
        }

        .logo-badge {
            font-size: 0.58rem;
            font-weight: 700;
            background: var(--lime-dim);
            border: 1px solid var(--lime-border);
            color: var(--lime);
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
            letter-spacing: 0.5px;
            margin-left: auto;
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .sidebar-nav::-webkit-scrollbar { display: none; }

        .nav-section-label {
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted2);
            padding: 0 0.5rem;
            margin: 1.25rem 0 0.5rem;
        }

        .nav-section-label:first-child { margin-top: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.75rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--muted);
            font-size: 0.875rem;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
            position: relative;
        }

        .nav-item:hover { background: var(--surface2); color: var(--text); }

        .nav-item.active {
            background: var(--lime-dim);
            color: var(--lime);
            font-weight: 600;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--lime);
            border-radius: 0 2px 2px 0;
        }

        .nav-icon {
            width: 18px; height: 18px;
            flex-shrink: 0;
            opacity: 0.7;
        }

        .nav-item.active .nav-icon { opacity: 1; }

        .nav-badge {
            margin-left: auto;
            font-size: 0.65rem;
            font-weight: 700;
            background: var(--danger);
            color: #fff;
            padding: 0.1rem 0.45rem;
            border-radius: 999px;
            min-width: 18px;
            text-align: center;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 0.875rem 1rem;
            border-top: 1px solid var(--border);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.5rem 0.5rem;
            border-radius: var(--radius-sm);
        }

        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--lime-dim);
            border: 1.5px solid var(--lime-border);
            display: grid;
            place-items: center;
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--lime);
            flex-shrink: 0;
        }

        .user-name { font-size: 0.82rem; font-weight: 600; }
        .user-role { font-size: 0.68rem; color: var(--muted); }

        .btn-logout {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--muted);
            padding: 0.35rem;
            border-radius: 6px;
            transition: color 0.15s, background 0.15s;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .btn-logout:hover { color: var(--danger); background: var(--danger-dim); }

        /* =====================
           TOPBAR
        ===================== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: rgba(8,8,8,0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 90;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: 1px solid var(--border);
            color: var(--text);
            width: 36px; height: 36px;
            border-radius: var(--radius-sm);
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .sidebar-toggle span {
            display: block;
            width: 16px; height: 1.5px;
            background: var(--text);
            border-radius: 1px;
            transition: all 0.3s;
        }

        .page-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
        }

        .page-breadcrumb {
            font-size: 0.75rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.05rem;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .topbar-time {
            font-size: 0.78rem;
            color: var(--muted);
            font-weight: 500;
        }

        /* =====================
           MAIN CONTENT
        ===================== */
        .main-wrap {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            flex: 1;
            min-height: calc(100vh - var(--topbar-h));
        }

        .main-content {
            padding: 1.75rem 1.5rem;
            max-width: 1400px;
        }

        /* =====================
           REUSABLE COMPONENTS
        ===================== */

        /* Cards */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 0.9rem;
            font-weight: 700;
        }

        .card-body { padding: 1.25rem; }

        /* Stat cards */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s, transform 0.2s;
        }

        .stat-card:hover { border-color: #333; transform: translateY(-1px); }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2px;
        }

        .stat-card.lime::after { background: var(--lime); }
        .stat-card.warning::after { background: var(--warning); }
        .stat-card.info::after { background: var(--info); }
        .stat-card.danger::after { background: var(--danger); }
        .stat-card.success::after { background: var(--success); }

        .stat-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .stat-icon.lime { background: var(--lime-dim); }
        .stat-icon.warning { background: var(--warning-dim); }
        .stat-icon.info { background: var(--info-dim); }
        .stat-icon.danger { background: var(--danger-dim); }
        .stat-icon.success { background: var(--success-dim); }

        .stat-val {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.3rem;
        }

        .stat-label { font-size: 0.78rem; color: var(--muted); font-weight: 500; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
            font-family: 'Outfit', sans-serif;
            white-space: nowrap;
        }

        .btn:active { transform: scale(0.96); }

        .btn-lime { background: var(--lime); color: #000; }
        .btn-lime:hover { background: #99ee00; }

        .btn-ghost { background: var(--surface2); color: var(--text); border: 1px solid var(--border); }
        .btn-ghost:hover { border-color: #444; }

        .btn-danger { background: var(--danger-dim); color: var(--danger); border: 1px solid rgba(255,68,68,0.2); }
        .btn-danger:hover { background: rgba(255,68,68,0.18); }

        .btn-success { background: var(--success-dim); color: var(--success); border: 1px solid rgba(74,222,128,0.2); }
        .btn-success:hover { background: rgba(74,222,128,0.18); }

        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.76rem; }
        .btn-icon { padding: 0.45rem; border-radius: var(--radius-sm); }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .badge-lime { background: var(--lime-dim); color: var(--lime); border: 1px solid var(--lime-border); }
        .badge-danger { background: var(--danger-dim); color: var(--danger); border: 1px solid rgba(255,68,68,0.15); }
        .badge-warning { background: var(--warning-dim); color: var(--warning); border: 1px solid rgba(245,158,11,0.2); }
        .badge-info { background: var(--info-dim); color: var(--info); border: 1px solid rgba(56,189,248,0.2); }
        .badge-success { background: var(--success-dim); color: var(--success); border: 1px solid rgba(74,222,128,0.2); }
        .badge-muted { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }

        /* Tables */
        .table-wrap {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        thead th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }

        /* Forms */
        .form-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            background: var(--surface2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.65rem 0.9rem;
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.15s;
            -webkit-appearance: none;
        }

        .form-input:focus { border-color: var(--lime); }
        .form-input::placeholder { color: var(--muted2); }

        select.form-input { cursor: pointer; }

        /* Alerts */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .alert-success { background: var(--success-dim); border: 1px solid rgba(74,222,128,0.2); color: var(--success); }
        .alert-error { background: var(--danger-dim); border: 1px solid rgba(255,68,68,0.2); color: var(--danger); }
        .alert-warning { background: var(--warning-dim); border: 1px solid rgba(245,158,11,0.2); color: var(--warning); }

        /* Grid utils */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
        .gap-1 { gap: 1rem; }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 99;
            backdrop-filter: blur(2px);
        }

        /* Paginate */
        .pagination {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
        }

        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 0.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            color: var(--muted);
            background: var(--surface2);
            border: 1px solid var(--border);
            transition: 0.15s;
        }

        .pagination a:hover { border-color: var(--lime); color: var(--lime); }
        .pagination .active, .pagination span[aria-current="page"] {
            background: var(--lime-dim);
            border-color: var(--lime-border);
            color: var(--lime);
        }

        /* =====================
           RESPONSIVE
        ===================== */
        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open { transform: translateX(0); }

            .sidebar-overlay.open { display: block; }

            .topbar { left: 0; }

            .main-wrap { margin-left: 0; }

            .sidebar-toggle { display: flex; }

            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 560px) {
            .main-content { padding: 1rem; }
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
            .topbar { padding: 0 1rem; }
            .topbar-time { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar Overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
    <a href="/dashboard" class="sidebar-logo">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M6 4v16M18 4v16M3 8h3M18 8h3M3 16h3M18 16h3M6 12h12" stroke="#000" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <span class="logo-text">JEFRYGYM</span>
        <span class="logo-badge">ADMIN</span>
    </a>

    <nav class="sidebar-nav">
        <span class="nav-section-label">Utama</span>

        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <span class="nav-section-label">Operasional</span>

        <a href="/absensi" class="nav-item {{ request()->is('absensi*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
            </svg>
            Absensi
        </a>

        <a href="/transaksi" class="nav-item {{ request()->is('transaksi*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
            Transaksi
        </a>

        <a href="/verifikasi" class="nav-item {{ request()->is('verifikasi*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9 12l2 2 4-4"/>
            </svg>
            Verifikasi
            @php $pendingCount = \App\Models\VerifikasiPembayaran::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="nav-badge">{{ $pendingCount }}</span>
            @endif
        </a>

        <span class="nav-section-label">Data</span>

        <a href="/member" class="nav-item {{ request()->is('member*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Member
        </a>

        <a href="/paket" class="nav-item {{ request()->is('paket*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
            </svg>
            Paket
        </a>

        <a href="/laporan" class="nav-item {{ request()->is('laporan*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
            Laporan
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="user-role">Administrator</div>
            </div>
            <form method="POST" action="/logout" style="margin-left:auto;">
                @csrf
                <button type="submit" class="btn-logout" title="Logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- TOPBAR --}}
<div class="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle">
            <span></span><span></span><span></span>
        </button>
        <div>
            <div class="page-title">@yield('title', 'Dashboard')</div>
            <div class="page-breadcrumb">
                JefryGym Admin
                @hasSection('breadcrumb')
                    <span>›</span> @yield('breadcrumb')
                @endif
            </div>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-time" id="topbarTime"></div>
    </div>
</div>

{{-- MAIN --}}
<div class="main-wrap">
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">✕ {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

<script>
    // Sidebar toggle mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    sidebarToggle?.addEventListener('click', () => {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });

    overlay?.addEventListener('click', closeSidebar);

    // Live clock topbar
    function updateClock() {
        const el = document.getElementById('topbarTime');
        if (!el) return;
        const now = new Date();
        el.textContent = now.toLocaleString('id-ID', {
            weekday: 'short', day: 'numeric', month: 'short',
            hour: '2-digit', minute: '2-digit'
        });
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>
@stack('scripts')
</body>
</html>