<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GymPro - @yield('title', 'Admin Panel')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-gym.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Sidebar link base */
        .sidebar-link {
            transition: all 0.18s ease;
            position: relative;
        }
        .sidebar-link:hover {
            background: rgba(255,255,255,0.06);
            color: #f9fafb;
        }
        .sidebar-link.active {
            background: rgba(16,185,129,0.12);
            color: #34d399;
            font-weight: 500;
        }
        .sidebar-link.active i {
            color: #34d399;
        }
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 3px;
            background: #34d399;
            border-radius: 0 3px 3px 0;
        }

        /* Nav group label */
        .nav-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #4b5563;
            padding: 0 12px;
            margin-top: 8px;
            margin-bottom: 2px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(156,163,175,0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; }

        /* Flash message animation */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .flash-msg { animation: slideDown 0.25s ease-out; }

        /* Topbar shadow subtle */
        .topbar-shadow { box-shadow: 0 1px 0 0 #f3f4f6; }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-100 text-gray-800 antialiased selection:bg-emerald-200 selection:text-emerald-900">

<div class="flex h-screen overflow-hidden w-full">

    {{-- MOBILE OVERLAY --}}
    <div id="sidebar-overlay" onclick="toggleSidebar()"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden opacity-0 transition-opacity">
    </div>

    {{-- ===================== SIDEBAR ===================== --}}
    <aside id="sidebar"
           class="fixed lg:static inset-y-0 left-0 z-50 w-[220px] bg-[#0f172a] flex flex-col flex-shrink-0
                  transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-white/[0.07]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-500/30">
                    <i class="fa-solid fa-dumbbell text-white text-[12px]"></i>
                </div>
                <div>
                    <div class="text-white font-bold text-[14px] tracking-wide leading-none">GymPro</div>
                    <div class="text-emerald-400/70 text-[10px] font-medium uppercase tracking-widest mt-0.5">Admin Panel</div>
                </div>
                <button onclick="toggleSidebar()" class="ml-auto lg:hidden text-gray-500 hover:text-white transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 py-3 overflow-y-auto px-2 space-y-0.5">

            {{-- MAIN --}}
            <p class="nav-label">Menu</p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-[12.5px] text-gray-400 rounded-lg
                      {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge w-4 text-center text-[13px] text-gray-500"></i>
                Dashboard
            </a>

            <a href="{{ route('member.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-[12.5px] text-gray-400 rounded-lg
                      {{ request()->routeIs('member.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users w-4 text-center text-[13px] text-gray-500"></i>
                Manajemen Member
            </a>

            <a href="{{ route('paket.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-[12.5px] text-gray-400 rounded-lg
                      {{ request()->routeIs('paket.*') ? 'active' : '' }}">
                <i class="fa-solid fa-box w-4 text-center text-[13px] text-gray-500"></i>
                Manajemen Paket
            </a>

            <a href="{{ route('absensi.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-[12.5px] text-gray-400 rounded-lg
                      {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock w-4 text-center text-[13px] text-gray-500"></i>
                Quick Absen
            </a>

            {{-- KEUANGAN --}}
            <p class="nav-label mt-3">Keuangan</p>

            <a href="{{ route('transaksi.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-[12.5px] text-gray-400 rounded-lg
                      {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register w-4 text-center text-[13px] text-gray-500"></i>
                Transaksi (Onsite)
            </a>

            <a href="{{ route('verifikasi.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-[12.5px] text-gray-400 rounded-lg
                      {{ request()->routeIs('verifikasi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice w-4 text-center text-[13px] text-gray-500"></i>
                <span class="flex-1">Verifikasi Online</span>
                @php $pending = \App\Models\VerifikasiPembayaran::where('status', 'pending')->count(); @endphp
                @if($pending > 0)
                    <span class="bg-orange-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none">
                        {{ $pending }}
                    </span>
                @endif
            </a>

            {{-- LAPORAN --}}
            <p class="nav-label mt-3">Analitik</p>

            <a href="{{ route('laporan.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-[12.5px] text-gray-400 rounded-lg
                      {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar w-4 text-center text-[13px] text-gray-500"></i>
                Laporan
            </a>

        </nav>

        {{-- User bottom --}}
        <div class="p-3 border-t border-white/[0.07]">
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-xl hover:bg-white/5 transition-colors group">
                <div class="w-7 h-7 rounded-lg bg-emerald-500 flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-gray-300 text-[11.5px] font-semibold truncate leading-none">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <div class="text-gray-500 text-[10px] truncate mt-0.5">{{ auth()->user()->email ?? 'admin@gympro.com' }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all"
                        title="Logout">
                        <i class="fa-solid fa-arrow-right-from-bracket text-[11px]"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===================== MAIN ===================== --}}
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">

        {{-- TOPBAR --}}
        <header class="bg-white topbar-shadow  px-4 lg:px-7 h-14 flex items-center justify-between flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg transition">
                    <i class="fa-solid fa-bars text-base"></i>
                </button>

                {{-- Breadcrumb feel --}}
                <div class="flex items-center gap-2 text-[13px]">
                    <span class="text-gray-400 font-medium">GymPro</span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
                    <span class="font-semibold text-gray-700">@yield('page-title', 'Dashboard')</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Tanggal --}}
                <div class="hidden md:flex items-center gap-1.5 text-[11.5px] text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-lg">
                    <i class="fa-regular fa-calendar text-[11px]"></i>
                    {{ now()->translatedFormat('d M Y') }}
                </div>

                {{-- Notif --}}
                <div class="relative">
                    <button class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                        <i class="fa-regular fa-bell text-[15px]"></i>
                    </button>
                    @if(isset($pending) && $pending > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-orange-500 rounded-full border border-white"></span>
                    @endif
                </div>

                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white text-[12px] font-bold cursor-pointer hover:bg-emerald-600 transition">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="flash-msg mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="flash-msg mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <i class="fa-solid fa-circle-xmark text-red-500"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')

        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        if (overlay.classList.contains('hidden')) {
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);
        } else {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }
</script>

@stack('scripts')
</body>
</html>