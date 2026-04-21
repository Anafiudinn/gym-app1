<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::getValue('nama_gym') }} – @yield('title', 'Admin Panel')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-gym.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f0fdf7',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        sidebar: '#0a0f1e',
                        'sidebar-border': 'rgba(255,255,255,0.05)',
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar {
            width: 3px;
            height: 3px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 99px;
        }

        /* ── Sidebar base ── */
        #sidebar {
            background: #0a0f1e;
            border-right: 1px solid rgba(255, 255, 255, 0.04);
        }

        /* ── Logo area ── */
        .sidebar-logo {
            height: 60px;
            padding: 0 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.3);
        }

        .logo-icon i {
            color: #fff;
            font-size: 13px;
        }

        .logo-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .logo-sub {
            font-size: 9px;
            color: rgba(16, 185, 129, 0.5);
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 600;
            margin-top: 1px;
        }

        /* ── Nav group label ── */
        .nav-group-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.13em;
            text-transform: uppercase;
            color: #2d3f55;
            padding: 0 12px;
            margin: 20px 0 5px;
        }

        /* ── Nav item ── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 6px 6px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 500;
            color: #b7c4d0;
            transition: background 0.15s, color 0.15s;
            position: relative;
            cursor: pointer;
            text-decoration: none;
            margin: 1px 0;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.04);
            color: #ffffff;
        }

        .nav-item .nav-icon {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            background: rgba(255, 255, 255, 0.03);
            flex-shrink: 0;
            transition: background 0.15s;
        }

        .nav-item .nav-icon i {
            font-size: 11px;
            color: #3d5068;
            transition: color 0.15s;
        }

        .nav-item:hover .nav-icon {
            background: rgba(255, 255, 255, 0.06);
        }

        .nav-item:hover .nav-icon i {
            color: #6b8aad;
        }

        /* Active state */
        .nav-item.active {
            background: rgba(16, 185, 129, 0.08);
            color: #34d399;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 18px;
            width: 2.5px;
            background: #10b981;
            border-radius: 0 2px 2px 0;
        }

        .nav-item.active .nav-icon {
            background: rgba(16, 185, 129, 0.12);
        }

        .nav-item.active .nav-icon i {
            color: #34d399;
        }

        /* ── User footer ── */
        .sidebar-user {
            padding: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 9px;
            transition: background 0.15s;
        }

        .user-card:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 12px;
            font-weight: 600;
            color: #cbd5e1;
            line-height: 1.2;
        }

        .user-email {
            font-size: 10px;
            color: #334155;
            margin-top: 1px;
        }

        /* ── Topbar ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            height: 58px;
        }

        /* ── Breadcrumb ── */
        .breadcrumb-sep {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #cbd5e1;
            display: inline-block;
            vertical-align: middle;
            margin: 0 6px;
        }

        /* ── Badge pending ── */
        .badge-pending {
            background: #f97316;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 99px;
            line-height: 1.4;
            min-width: 16px;
            text-align: center;
        }

        /* ---- DROPDOWN AVATAR ---- */
        .avatar-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #fff;
            border: 1px solid #eaecf4;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(30, 33, 57, 0.10);
            min-width: 180px;
            z-index: 100;
            animation: fadeSlideDown 0.15s ease-out;
        }

        /* ── Topbar bell dot ── */
        .bell-dot {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 6px;
            height: 6px;
            background: #f97316;
            border-radius: 50%;
            border: 1.5px solid #fff;
        }

        /* ── SweetAlert2 Theme ── */
        .swal2-popup {
            border-radius: 16px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            padding: 28px 24px !important;
        }

        .swal2-title {
            font-size: 16px !important;
            font-weight: 700 !important;
        }

        .swal2-html-container {
            font-size: 13px !important;
            color: #64748b !important;
        }

        .swal2-confirm {
            border-radius: 9px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            padding: 9px 20px !important;
            box-shadow: none !important;
        }

        .swal2-cancel {
            border-radius: 9px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            padding: 9px 20px !important;
            box-shadow: none !important;
        }

        /* ── Overlay ── */
        #sidebar-overlay {
            backdrop-filter: blur(4px);
        }

        /* ── Flash ── */
        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateY(-6px);
                pointer-events: none;
            }
        }

        .flash-dismiss {
            animation: fadeOut .4s ease forwards;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-100 text-gray-800 antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- ===== MOBILE OVERLAY ===== --}}
        <div id="sidebar-overlay" onclick="closeSidebar()"
            class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300 lg:hidden"></div>

        {{-- ===== SIDEBAR ===== --}}
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-[220px] flex flex-col
                  transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex-shrink-0">

            {{-- Logo --}}
            <div class="sidebar-logo">
                <div class="logo-icon">
                    <i class="fa-solid fa-dumbbell"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="logo-name truncate">{{ \App\Models\Setting::getValue('nama_gym') }}</div>
                    <div class="logo-sub">Admin Panel</div>
                </div>
                <button onclick="closeSidebar()"
                    class="lg:hidden w-6 h-6 flex items-center justify-center text-gray-600 hover:text-gray-400 rounded transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-2 px-3 space-y-0">

                <p class="nav-group-label">Menu Utama</p>

                <a href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span>
                    Dashboard
                </a>

                <a href="{{ route('member.index') }}"
                    class="nav-item {{ request()->routeIs('member.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
                    Manajemen Member
                </a>

                <a href="{{ route('paket.index') }}"
                    class="nav-item {{ request()->routeIs('paket.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-box-open"></i></span>
                    Manajemen Paket
                </a>

                <p class="nav-group-label">Absensi</p>

                <a href="{{ route('absensi.index') }}"
                    class="nav-item {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-fingerprint"></i></span>
                    Quick Absen
                </a>

                <p class="nav-group-label">Keuangan</p>

                <a href="{{ route('transaksi.index') }}"
                    class="nav-item {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span>
                    Transaksi Onsite
                </a>

                <a href="{{ route('verifikasi.index') }}"
                    class="nav-item {{ request()->routeIs('verifikasi.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-file-circle-check"></i></span>
                    <span class="flex-1 min-w-0 truncate">Verifikasi Online</span>
                    @php $pending = \App\Models\VerifikasiPembayaran::where('status', 'pending')->count(); @endphp
                    @if($pending > 0)
                        <span class="badge-pending">{{ $pending }}</span>
                    @endif
                </a>

                <a href="{{ route('riwayat.index') }}"
                    class="nav-item {{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                    Riwayat Transaksi
                </a>

                <p class="nav-group-label">Analitik</p>

                <a href="{{ route('laporan.index') }}"
                    class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
                    Laporan
                </a>

                <p class="nav-group-label">Sistem</p>

                <a href="{{ route('settings.index') }}"
                    class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-sliders"></i></span>
                    Pengaturan Gym
                </a>
                {{--logs wa --}}
                @php
                    // Kita hitung jumlah status 'failed'
                    // Kamu bisa batasi 'failed' dalam 24 jam terakhir atau semuanya
                    $failedCount = \App\Models\WaLog::where('status', 'failed')->count();
                @endphp

                <a href="{{ route('wa-logs.index') }}"
                    class="nav-item {{ request()->routeIs('wa-logs.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                    <span class="text-[13px]">Logs Wa</span>

    {{-- Munculkan angka merah hanya jika ada yang gagal --}}
    @if($failedCount > 0)
        <span
            class="flex items-center justify-center bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-bounce">
            {{ $failedCount }}
        </span>
    @endif
    </a>
    {{-- Bagian Status Layanan di Sidebar --}}
    <p class="nav-group-label">Status Layanan</p>

    <div class="nav-item cursor-default group">
        <span class="nav-icon">
            {{-- Kita kasih ID wa-icon, warna default abu-abu --}}
            <i id="wa-icon" class="fa-brands fa-whatsapp text-gray-400"></i>
        </span>
        <span class="flex-1 text-[13px] text-gray-400">
            WA: <strong id="wa-text">Checking...</strong>
        </span>

        {{-- Tempat indikator titik (dot) atau icon error --}}
        <div id="wa-status-container">
            {{-- Muncul loading kecil atau biarkan kosong dulu --}}
            <i class="fa-solid fa-spinner fa-spin text-gray-300 text-[10px]"></i>
        </div>
    </div>

    </nav>

    {{-- User footer --}}
    <div class="sidebar-user">
        <div class="user-card">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="user-name truncate">{{ auth()->user()->name ?? 'Administrator' }}</div>
                <div class="user-email truncate">{{ auth()->user()->email ?? 'admin@gympro.com' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" onclick="return confirmLogout(event)"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-600 hover:text-red-400 hover:bg-red-500/10 transition"
                    title="Logout">
                    <i class="fa-solid fa-right-from-bracket text-[10px]"></i>
                </button>
            </form>
        </div>
    </div>
    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- TOPBAR --}}
        <header class="topbar px-4 lg:px-6 flex items-center justify-between flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                {{-- Hamburger --}}
                <button onclick="openSidebar()"
                    class="lg:hidden p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg transition">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>

                {{-- Breadcrumb --}}
                <div class="flex items-center text-[13px]">
                    <span class="text-gray-400 font-medium">GymPro</span>
                    <span class="breadcrumb-sep"></span>
                    <span class="font-600 text-gray-700 font-semibold">@yield('page-title', 'Dashboard')</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Date chip --}}
                <div
                    class="hidden sm:flex items-center gap-1.5 text-[11.5px] text-gray-500 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-lg font-medium">
                    <i class="fa-regular fa-calendar-days text-[10px] text-gray-400"></i>
                    {{ now()->translatedFormat('d M Y') }}
                </div>

                {{-- Notification --}}
                <div class="relative">
                    <button
                        class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                        <i class="fa-regular fa-bell text-[13px]"></i>
                    </button>
                    @if(isset($pending) && $pending > 0)
                        <span class="bell-dot"></span>
                    @endif
                </div>

                {{-- Avatar chip --}}
                {{-- Avatar + Dropdown --}}
                <div class="relative ml-1" id="avatar-wrapper">
                    <button onclick="toggleAvatarMenu()"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold transition-transform hover:scale-105"
                        style="background: linear-gradient(135deg, #10b981, #059669); box-shadow:0 2px 8px rgba(37, 201, 116, 0.3);">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </button>
                    <div id="avatar-menu" class="avatar-menu hidden" style="right:0;">
                        <div class="px-4 py-3 border-b" style="border-color:#eaecf4;">
                            <div class="text-sm font-semibold text-gray-800">
                                {{ auth()->user()->name ?? 'Administrator' }}
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ auth()->user()->email ?? 'admin@gympro.com'
                                }}</div>
                        </div>
                        <div class="p-1.5">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                                <i class="fa-regular fa-user w-4 text-center text-gray-400 text-xs"></i> Profil
                            </a>
                            <a href="{{ route('settings.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-gear w-4 text-center text-gray-400 text-xs"></i> Pengaturan
                            </a>
                            <div class="border-t my-1" style="border-color:#eaecf4;"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 transition text-left">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center text-xs"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">

            {{-- Flash Messages via SweetAlert2 --}}
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'success', title: 'Berhasil!', text: @json(session('success')),
                            confirmButtonColor: '#10b981', confirmButtonText: 'OK', timer: 3500, timerProgressBar: true
                        });
                    });
                </script>
            @endif
            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'error', title: 'Gagal!', text: @json(session('error')),
                            confirmButtonColor: '#ef4444', confirmButtonText: 'Tutup'
                        });
                    });
                </script>
            @endif
            @if(session('warning'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'warning', title: 'Perhatian!', text: @json(session('warning')),
                            confirmButtonColor: '#f59e0b', confirmButtonText: 'Mengerti'
                        });
                    });
                </script>
            @endif
            @if(session('info'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'info', title: 'Informasi', text: @json(session('info')),
                            confirmButtonColor: '#3b82f6', confirmButtonText: 'OK'
                        });
                    });
                </script>
            @endif

            @yield('content')
        </main>
    </div>
    </div>

    <script>
        /* ── Sidebar toggle ── */
        function openSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('sidebar-overlay');
            s.classList.remove('-translate-x-full');
            o.classList.remove('hidden');
            setTimeout(() => o.classList.remove('opacity-0'), 10);
        }
        function closeSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('sidebar-overlay');
            s.classList.add('-translate-x-full');
            o.classList.add('opacity-0');
            setTimeout(() => o.classList.add('hidden'), 300);
        }

        /* ── SweetAlert2 Toast mixin ── */
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false,
            timer: 3000, timerProgressBar: true,
            customClass: { popup: 'rounded-xl text-sm' },
            didOpen: (t) => {
                t.addEventListener('mouseenter', Swal.stopTimer);
                t.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Object.assign(Swal, {
            success(text, title = 'Berhasil!') {
                return Swal.fire({ icon: 'success', title, text, confirmButtonColor: '#10b981', confirmButtonText: 'OK', timer: 3000, timerProgressBar: true });
            },
            error(text, title = 'Gagal!') {
                return Swal.fire({ icon: 'error', title, text, confirmButtonColor: '#ef4444', confirmButtonText: 'Tutup' });
            },
            warning(text, title = 'Perhatian!') {
                return Swal.fire({ icon: 'warning', title, text, confirmButtonColor: '#f59e0b', confirmButtonText: 'Mengerti' });
            },
            info(text, title = 'Informasi') {
                return Swal.fire({ icon: 'info', title, text, confirmButtonColor: '#3b82f6', confirmButtonText: 'OK' });
            },
            toast(text, icon = 'success') { return Toast.fire({ icon, title: text }); },

            confirm({ title = 'Apakah Anda yakin?', text = '', confirmText = 'Ya, lanjutkan', cancelText = 'Batal', onConfirm } = {}) {
                Swal.fire({
                    icon: 'question', title, text, showCancelButton: true,
                    confirmButtonColor: '#10b981', cancelButtonColor: '#e5e7eb',
                    confirmButtonText: `<i class="fa-solid fa-check mr-1"></i>${confirmText}`,
                    cancelButtonText: cancelText, reverseButtons: true,
                    customClass: { cancelButton: '!text-gray-700' }
                }).then(r => { if (r.isConfirmed && typeof onConfirm === 'function') onConfirm(); });
            },

            deleteConfirm(form, itemName = 'data ini') {
                Swal.fire({
                    icon: 'warning', title: 'Hapus Data?',
                    html: `Data <strong>${itemName}</strong> akan dihapus permanen dan tidak bisa dikembalikan.`,
                    showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#e5e7eb',
                    confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i>Ya, hapus',
                    cancelButtonText: 'Batal', reverseButtons: true,
                    customClass: { cancelButton: '!text-gray-700' }
                }).then(r => { if (r.isConfirmed) form.submit(); });
            },
        });

        /* ---- AVATAR DROPDOWN ---- */
        function toggleAvatarMenu() {
            document.getElementById('avatar-menu').classList.toggle('hidden');
        }
        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('avatar-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('avatar-menu').classList.add('hidden');
            }
        });


        /* ── Logout confirm ── */
        function confirmLogout(e) {
            e.preventDefault();
            const form = e.currentTarget.closest('form');
            Swal.fire({
                icon: 'question', title: 'Keluar?', text: 'Sesi Anda akan diakhiri.',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#e5e7eb',
                confirmButtonText: '<i class="fa-solid fa-right-from-bracket mr-1"></i>Logout',
                cancelButtonText: 'Batal', reverseButtons: true,
                customClass: { cancelButton: '!text-gray-700' }
            }).then(r => { if (r.isConfirmed) form.submit(); });
        }

        function updateWaStatus() {
            const icon = document.getElementById('wa-icon');
            const text = document.getElementById('wa-text');
            const container = document.getElementById('wa-status-container');

            // Panggil API yang sudah kita buat tadi (pakai slash akhir sesuai temuan kamu)
            fetch("/api/wa-status/")
                .then(response => response.json())
                .then(data => {
                    if (data.connected) {
                        // Tampilan saat CONNECTED
                        icon.classList.remove('text-gray-400', 'text-red-500');
                        icon.classList.add('text-emerald-500');

                        text.innerText = 'Connected';
                        text.parentElement.classList.remove('text-gray-400', 'text-red-600');
                        text.parentElement.classList.add('text-gray-600');

                        container.innerHTML = `
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    `;
                    } else {
                        // Tampilan saat DISCONNECTED
                        icon.classList.remove('text-gray-400', 'text-emerald-500');
                        icon.classList.add('text-red-500');

                        text.innerText = 'Disconnected';
                        text.parentElement.classList.remove('text-gray-400', 'text-gray-600');
                        text.parentElement.classList.add('text-red-600');

                        container.innerHTML = '<i class="fa-solid fa-circle-exclamation text-red-500 animate-pulse"></i>';
                    }
                })
                .catch(error => {
                    text.innerText = 'API Error';
                    container.innerHTML = '';
                    console.error('WA Status Error:', error);
                });
        }

        window.addEventListener('pageshow', function(event) {
    // Cek apakah halaman dimuat dari cache (tombol back/forward)
    if (event.persisted || (typeof window.performance !== 'undefined' && window.performance.navigation.type === 2)) {
        // Tutup SweetAlert secara paksa jika dia mencoba muncul
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }
});

        // Jalankan saat halaman siap
        document.addEventListener('DOMContentLoaded', updateWaStatus);
    </script>

    @stack('scripts')
</body>

</html>