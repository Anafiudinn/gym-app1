<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::getValue('nama_gym','GYMKU SEMARANG') }} — @yield('title', 'Admin Panel')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-gym.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --sidebar-bg: #1e2139;
            --sidebar-hover: rgba(255,255,255,0.05);
            --sidebar-active-bg: rgba(99,102,241,0.15);
            --sidebar-active-color: #818cf8;
            --sidebar-active-bar: #6366f1;
            --accent: #6366f1;
            --accent-light: #818cf8;
            --text-sidebar: #8b8fa8;
            --label-color: #4b5066;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f2f9;
            color: #1e2139;
        }

        /* ---- SIDEBAR ---- */
        #sidebar {
            background: var(--sidebar-bg);
            width: 240px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-sidebar);
            transition: background 0.15s, color 0.15s;
            position: relative;
            cursor: pointer;
            text-decoration: none;
        }
        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #d1d5db;
        }
        .sidebar-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-color);
        }
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; height: 60%; width: 3px;
            background: var(--sidebar-active-bar);
            border-radius: 0 3px 3px 0;
        }
        .sidebar-link.active .nav-icon,
        .sidebar-link.active i {
            color: var(--sidebar-active-color);
        }
        .nav-icon {
            width: 16px;
            text-align: center;
            font-size: 13px;
            color: #555a7a;
            flex-shrink: 0;
        }
        .nav-group-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: var(--label-color);
            padding: 6px 14px 4px;
            margin-top: 6px;
        }

        /* Collapsible sub-menu */
        .has-sub > .sub-arrow {
            margin-left: auto;
            transition: transform 0.2s;
            font-size: 10px;
        }
        .has-sub.open > .sub-arrow { transform: rotate(90deg); }
        .sub-menu {
            display: none;
            padding-left: 28px;
            margin-top: 2px;
        }
        .sub-menu.open { display: block; }
        .sub-menu a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 6px;
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
            transition: all 0.15s;
            text-decoration: none;
        }
        .sub-menu a:hover { background: rgba(255,255,255,0.04); color: #d1d5db; }
        .sub-menu a.active { color: var(--sidebar-active-color); }

        /* ---- TOPBAR ---- */
        #topbar {
            background: #fff;
            border-bottom: 1px solid #eaecf4;
            height: 60px;
        }

        /* ---- MAIN ---- */
        #main-content {
            background: #f0f2f9;
        }

        /* ---- BREADCRUMB ---- */
        .breadcrumb-sep {
            font-size: 10px;
            color: #c5c8d8;
        }

        /* ---- SEARCH BAR ---- */
        #search-input {
            background: #f5f6fa;
            border: 1px solid #e4e6f0;
            border-radius: 8px;
            padding: 6px 12px 6px 36px;
            font-size: 13px;
            color: #1e2139;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            width: 240px;
            transition: border-color 0.15s, width 0.25s;
        }
        #search-input:focus {
            border-color: var(--accent);
            width: 300px;
            background: #fff;
        }
        #search-input::placeholder { color: #a0a3b8; }

        /* ---- STAT CARDS ---- */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: 0 1px 4px rgba(30,33,57,0.04);
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .stat-card:hover {
            box-shadow: 0 4px 16px rgba(30,33,57,0.08);
            transform: translateY(-1px);
        }
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        /* ---- BADGE / STATUS ---- */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-open   { background: #dcfce7; color: #16a34a; }
        .badge-closed { background: #f1f5f9; color: #475569; }
        .badge-pending { background: #fff7ed; color: #ea580c; }

        /* ---- TABLE ---- */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            text-align: left;
            font-size: 11.5px;
            font-weight: 700;
            color: #8b8fa8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 10px 14px;
            border-bottom: 1px solid #eaecf4;
        }
        .data-table tbody tr {
            border-bottom: 1px solid #f3f4f9;
            transition: background 0.12s;
        }
        .data-table tbody tr:hover { background: #f8f9fe; }
        .data-table tbody td { padding: 11px 14px; font-size: 13px; color: #3d4166; }

        /* ---- ICON BUTTON ---- */
        .icon-btn {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 13px;
            transition: background 0.15s, color 0.15s;
            cursor: pointer;
            color: #a0a3b8;
            border: none; background: transparent;
        }
        .icon-btn:hover { background: #f0f2f9; color: #1e2139; }

        /* ---- SCROLLBAR ---- */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.2); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,0.5); }

        /* ---- MOBILE OVERLAY ---- */
        #sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(2px);
            z-index: 40;
        }

        /* ---- ANIMATIONS ---- */
        @keyframes fadeSlideDown {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .flash-msg { animation: fadeSlideDown 0.2s ease-out; }

        /* ---- DROPDOWN AVATAR ---- */
        .avatar-menu {
            position: absolute; right: 0; top: calc(100% + 8px);
            background: #fff;
            border: 1px solid #eaecf4;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(30,33,57,0.10);
            min-width: 180px;
            z-index: 100;
            animation: fadeSlideDown 0.15s ease-out;
        }

        /* Pending dot */
        .notif-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: #f97316;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* ---- PAGE HEADER ---- */
        .page-header {
            margin-bottom: 20px;
        }
        .page-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1e2139;
            margin: 0;
        }
        .page-header .page-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #a0a3b8;
            margin-top: 2px;
        }
        .page-header .page-breadcrumb .current {
            color: var(--accent);
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>

<body class="antialiased">
<div class="flex h-screen overflow-hidden w-full">

    {{-- MOBILE OVERLAY --}}
    <div id="sidebar-overlay" onclick="closeSidebar()" class="hidden lg:hidden"></div>

    {{-- ============================================================ --}}
    {{-- SIDEBAR --}}
    {{-- ============================================================ --}}
    <aside id="sidebar"
           class="fixed lg:static inset-y-0 left-0 z-50 flex flex-col flex-shrink-0
                  transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
           style="width:240px; background:var(--sidebar-bg);">

        {{-- Logo --}}
        <div class="px-5 py-4 flex items-center gap-3 border-b" style="border-color:rgba(255,255,255,0.06);">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: linear-gradient(135deg,#6366f1,#818cf8); box-shadow:0 4px 12px rgba(99,102,241,0.4);">
                <i class="fa-solid fa-dumbbell text-white text-sm"></i>
            </div>
            <div>
                <div class="text-white font-bold text-sm tracking-wide leading-none">{{ \App\Models\Setting::getValue('nama_gym','GYMKU SEMARANG') }}</div>
                <div class="text-xs font-medium mt-0.5" style="color:#555a7a; letter-spacing:0.06em;">Admin Panel</div>
            </div>
            {{-- Mobile close --}}
            <button onclick="closeSidebar()" class="ml-auto lg:hidden text-gray-500 hover:text-white transition text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

            {{-- MENU --}}
            <p class="nav-group-label">Menu</p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge nav-icon"></i>
                Dashboard
            </a>

            <a href="{{ route('member.index') }}"
               class="sidebar-link {{ request()->routeIs('member.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users nav-icon"></i>
                Manajemen Member
            </a>

            <a href="{{ route('paket.index') }}"
               class="sidebar-link {{ request()->routeIs('paket.*') ? 'active' : '' }}">
                <i class="fa-solid fa-box nav-icon"></i>
                Manajemen Paket
            </a>

            <a href="{{ route('absensi.index') }}"
               class="sidebar-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock nav-icon"></i>
                Quick Absen
            </a>

            {{-- KEUANGAN --}}
            <p class="nav-group-label" style="margin-top:16px;">Keuangan</p>

            <a href="{{ route('transaksi.index') }}"
               class="sidebar-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register nav-icon"></i>
                Transaksi (Onsite)
            </a>

            <a href="{{ route('verifikasi.index') }}"
               class="sidebar-link {{ request()->routeIs('verifikasi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice nav-icon"></i>
                <span class="flex-1">Verifikasi Online</span>
                @php $pending = \App\Models\VerifikasiPembayaran::where('status','pending')->count(); @endphp
                @if($pending > 0)
                    <span class="text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none"
                          style="background:#f97316; color:#fff; font-size:9px;">{{ $pending }}</span>
                @endif
            </a>

            <a href="{{ route('riwayat.index') }}"
               class="sidebar-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left nav-icon"></i>
                Riwayat Transaksi
            </a>

            {{-- ANALITIK --}}
            <p class="nav-group-label" style="margin-top:16px;">Analitik</p>

            <a href="{{ route('laporan.index') }}"
               class="sidebar-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar nav-icon"></i>
                Laporan
            </a>

            {{-- SETTINGS --}}
            <p class="nav-group-label" style="margin-top:16px;">Pengaturan</p>
            <a href="{{ route('settings.index') }}"
               class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear nav-icon"></i>
                Pengaturan Aplikasi
            </a>

        </nav>

        {{-- User Section --}}
        <div class="p-3 border-t" style="border-color:rgba(255,255,255,0.06);">
            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-colors" style="background:rgba(255,255,255,0.04);">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                     style="background: linear-gradient(135deg,#6366f1,#818cf8);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold truncate leading-none" style="color:#d1d5db;">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </div>
                    <div class="text-xs truncate mt-0.5" style="color:#555a7a;">
                        {{ auth()->user()->email ?? 'admin@gympro.com' }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="icon-btn"
                        style="color:#555a7a;"
                        title="Logout"
                        onmouseover="this.style.color='#f87171'; this.style.background='rgba(239,68,68,0.1)';"
                        onmouseout="this.style.color='#555a7a'; this.style.background='transparent';">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ============================================================ --}}
    {{-- MAIN AREA --}}
    {{-- ============================================================ --}}
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden" id="main-content">

        {{-- ---- TOPBAR ---- --}}
        <header id="topbar" class="flex items-center justify-between px-5 lg:px-8 flex-shrink-0 z-10">

            {{-- Left: hamburger + search --}}
            <div class="flex items-center gap-3">
                {{-- Mobile hamburger --}}
                <button onclick="openSidebar()" class="lg:hidden icon-btn" style="color:#8b8fa8;">
                    <i class="fa-solid fa-bars text-base"></i>
                </button>

                {{-- Desktop hamburger (collapse sidebar) --}}
                <button onclick="toggleDesktopSidebar()" class="hidden lg:flex icon-btn" style="color:#8b8fa8;" title="Toggle sidebar">
                    <i class="fa-solid fa-bars-staggered text-base"></i>
                </button>

                {{-- Search --}}
                <div class="relative hidden sm:block">
                    <i class="fa-solid fa-magnifying-glass text-xs absolute left-3 top-1/2 -translate-y-1/2" style="color:#a0a3b8;"></i>
                    <input id="search-input" type="text" placeholder="Cari sesuatu..." autocomplete="off">
                </div>
            </div>

            {{-- Right: date · notif · avatar --}}
            <div class="flex items-center gap-1.5">

                {{-- Date badge --}}
                <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium"
                     style="background:#f5f6fa; border:1px solid #eaecf4; color:#8b8fa8;">
                    <i class="fa-regular fa-calendar text-xs"></i>
                    {{ now()->translatedFormat('d M Y') }}
                </div>

                {{-- Notification --}}
                <div class="relative">
                    <button class="icon-btn relative" style="color:#8b8fa8;" title="Notifikasi">
                        <i class="fa-regular fa-bell text-base"></i>
                        @if(isset($pending) && $pending > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </button>
                </div>

                {{-- Theme toggle (UI only) --}}
                <button class="icon-btn" style="color:#8b8fa8;" title="Tema">
                    <i class="fa-regular fa-moon text-base"></i>
                </button>

                {{-- Avatar + Dropdown --}}
                <div class="relative ml-1" id="avatar-wrapper">
                    <button onclick="toggleAvatarMenu()"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold transition-transform hover:scale-105"
                        style="background: linear-gradient(135deg,#6366f1,#818cf8); box-shadow:0 2px 8px rgba(99,102,241,0.3);">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </button>
                    <div id="avatar-menu" class="avatar-menu hidden" style="right:0;">
                        <div class="px-4 py-3 border-b" style="border-color:#eaecf4;">
                            <div class="text-sm font-semibold text-gray-800">{{ auth()->user()->name ?? 'Administrator' }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ auth()->user()->email ?? 'admin@gympro.com' }}</div>
                        </div>
                        <div class="p-1.5">
                            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                                <i class="fa-regular fa-user w-4 text-center text-gray-400 text-xs"></i> Profil
                            </a>
                            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-gear w-4 text-center text-gray-400 text-xs"></i> Pengaturan
                            </a>
                            <div class="border-t my-1" style="border-color:#eaecf4;"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 transition text-left">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center text-xs"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- ---- CONTENT ---- --}}
        <main class="flex-1 overflow-y-auto p-5 lg:p-7">

            {{-- Page Header --}}
            <div class="page-header">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <div class="page-breadcrumb">
                    <span>GymPro</span>
                    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
                    <span class="current">@yield('page-title', 'Dashboard')</span>
                </div>
            </div>

            {{-- Flash Messages → SweetAlert2 --}}
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        GymProAlert.success('Berhasil!', '{{ addslashes(session('success')) }}');
                    });
                </script>
            @endif
            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        GymProAlert.error('Oops!', '{{ addslashes(session('error')) }}');
                    });
                </script>
            @endif

            {{-- Fallback flash (non-JS) --}}
            @if(session('success'))
                <div class="flash-msg mb-4 flex items-center gap-3 rounded-xl px-4 py-3 text-sm"
                     style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d;">
                    <i class="fa-solid fa-circle-check" style="color:#22c55e;"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="flash-msg mb-4 flex items-center gap-3 rounded-xl px-4 py-3 text-sm"
                     style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626;">
                    <i class="fa-solid fa-circle-xmark" style="color:#ef4444;"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')

        </main>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SWEETALERT2 SYSTEM --}}
{{-- ============================================================ --}}
<script>
class GymProAlert {
    static async confirm(title, text, confirmText = 'Ya, lanjutkan', cancelText = 'Batal') {
        const result = await Swal.fire({
            title, text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            buttonsStyling: false,
            customClass: {
                confirmButton: 'px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg font-medium text-sm ml-2 transition',
                cancelButton:  'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium text-sm transition'
            },
            reverseButtons: true,
            heightAuto: false
        });
        return result.isConfirmed;
    }

    static async success(title, message) {
        await Swal.fire({
            title, text: message, icon: 'success',
            confirmButtonColor: '#6366f1',
            customClass: { confirmButton: 'px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg font-medium text-sm' },
            timer: 3000, timerProgressBar: true
        });
    }

    static async error(title, message) {
        await Swal.fire({
            title, text: message, icon: 'error',
            confirmButtonColor: '#ef4444',
            customClass: { confirmButton: 'px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium text-sm' }
        });
    }
}
</script>

{{-- ============================================================ --}}
{{-- UI SCRIPTS --}}
{{-- ============================================================ --}}
<script>
    /* ---- SIDEBAR ---- */
    function openSidebar() {
        const sb = document.getElementById('sidebar');
        const ov = document.getElementById('sidebar-overlay');
        sb.classList.remove('-translate-x-full');
        ov.classList.remove('hidden');
    }
    function closeSidebar() {
        const sb = document.getElementById('sidebar');
        const ov = document.getElementById('sidebar-overlay');
        sb.classList.add('-translate-x-full');
        ov.classList.add('hidden');
    }

    let desktopSidebarVisible = true;
    function toggleDesktopSidebar() {
        const sb  = document.getElementById('sidebar');
        const mc  = document.getElementById('main-content');
        desktopSidebarVisible = !desktopSidebarVisible;
        if (!desktopSidebarVisible) {
            sb.style.width = '0';
            sb.style.overflow = 'hidden';
        } else {
            sb.style.width = '240px';
            sb.style.overflow = '';
        }
    }

    /* ---- AVATAR DROPDOWN ---- */
    function toggleAvatarMenu() {
        document.getElementById('avatar-menu').classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('avatar-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('avatar-menu').classList.add('hidden');
        }
    });

    /* ---- SUB MENUS ---- */
    document.querySelectorAll('.has-sub').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('open');
            const sub = this.nextElementSibling;
            if (sub && sub.classList.contains('sub-menu')) {
                sub.classList.toggle('open');
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>