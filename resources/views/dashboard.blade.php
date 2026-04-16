@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    @keyframes countUp {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-card { animation: countUp 0.4s ease-out both; }
    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }
    .stat-card:nth-child(4) { animation-delay: 0.26s; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .section-fade { animation: fadeUp 0.5s ease-out 0.3s both; }

    .ring-chart-wrap { position: relative; width: 120px; height: 120px; }
    .ring-chart-wrap canvas { position: absolute; inset: 0; }
    .ring-label {
        position: absolute; inset: 0;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        pointer-events: none;
    }

    /* Sparkline bars */
    .spark-bar {
        flex: 1;
        background: #d1fae5;
        border-radius: 3px 3px 0 0;
        transition: background 0.15s;
        min-height: 4px;
    }
    .spark-bar:hover { background: #34d399; }
    .spark-bar.today { background: #10b981; }
</style>
@endpush

@section('content')

{{-- ===== GREETING ===== --}}
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">
        Selamat datang, {{ auth()->user()->name ?? 'Admin' }} 👋
    </h1>
    <p class="text-sm text-gray-400 mt-0.5">
        Ringkasan aktivitas gym hari ini — {{ now()->translatedFormat('l, d F Y') }}
    </p>
</div>

{{-- ===== STAT CARDS ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Pendapatan Hari Ini --}}
    <div class="stat-card bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fa-solid fa-money-bill-wave text-emerald-500 text-sm"></i>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                {{ $growthPendapatan >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                {{ $growthPendapatan >= 0 ? '+' : '' }}{{ $growthPendapatan }}%
            </span>
        </div>
        <div class="text-xl font-bold text-gray-800 leading-tight">
            Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
        </div>
        <div class="text-[11.5px] text-gray-400 mt-1">Pendapatan Hari Ini</div>
        <div class="text-[10.5px] text-gray-300 mt-0.5">
            Kemarin: Rp {{ number_format($pendapatanKemarin, 0, ',', '.') }}
        </div>
    </div>

    {{-- Member Aktif --}}
    <div class="stat-card bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="fa-solid fa-users text-blue-500 text-sm"></i>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600">
                Aktif
            </span>
        </div>
        <div class="text-xl font-bold text-gray-800 leading-tight">
            {{ number_format($memberAktif) }}
        </div>
        <div class="text-[11.5px] text-gray-400 mt-1">Member Aktif</div>
        <div class="text-[10.5px] text-gray-300 mt-0.5">
            Total: {{ number_format($totalMember) }} terdaftar
        </div>
    </div>

    {{-- Kunjungan Hari Ini --}}
    <div class="stat-card bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
                <i class="fa-solid fa-person-walking-arrow-right text-violet-500 text-sm"></i>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-violet-50 text-violet-600">
                Hari ini
            </span>
        </div>
        <div class="text-xl font-bold text-gray-800 leading-tight">
            {{ $kunjunganHariIni }}
        </div>
        <div class="text-[11.5px] text-gray-400 mt-1">Kunjungan Hari Ini</div>
        <div class="text-[10.5px] text-gray-300 mt-0.5">
            Kemarin: {{ $kunjunganKemarin }} kunjungan
        </div>
    </div>

    {{-- Pending Verifikasi --}}
    <div class="stat-card bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow
                {{ $pendingVerifikasi > 0 ? 'ring-2 ring-orange-300 ring-offset-1' : '' }}">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl {{ $pendingVerifikasi > 0 ? 'bg-orange-50' : 'bg-gray-50' }} flex items-center justify-center">
                <i class="fa-solid fa-file-invoice text-{{ $pendingVerifikasi > 0 ? 'orange' : 'gray' }}-400 text-sm"></i>
            </div>
            @if($pendingVerifikasi > 0)
                <span class="flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-orange-100 text-orange-600 animate-pulse">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 inline-block"></span>
                    Perlu Aksi
                </span>
            @endif
        </div>
        <div class="text-xl font-bold {{ $pendingVerifikasi > 0 ? 'text-orange-500' : 'text-gray-800' }} leading-tight">
            {{ $pendingVerifikasi }}
        </div>
        <div class="text-[11.5px] text-gray-400 mt-1">Pending Verifikasi</div>
        @if($pendingVerifikasi > 0)
            <a href="{{ route('verifikasi.index') }}"
               class="inline-block mt-1.5 text-[10.5px] text-orange-500 font-semibold hover:underline">
                Lihat sekarang →
            </a>
        @else
            <div class="text-[10.5px] text-gray-300 mt-0.5">Semua sudah diverifikasi</div>
        @endif
    </div>

</div>

{{-- ===== ROW 2: Chart + Mini Stats ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4 section-fade">

    {{-- Grafik Pendapatan 7 Hari --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-[13.5px] font-bold text-gray-700">Pendapatan 7 Hari Terakhir</h2>
                <p class="text-[11px] text-gray-400 mt-0.5">Transaksi berstatus dibayar</p>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold text-emerald-600">
                    Rp {{ number_format($totalPendapatan7Hari, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-gray-400">Total 7 hari</div>
            </div>
        </div>
        <div style="height: 200px;">
            <canvas id="chartPendapatan"></canvas>
        </div>
    </div>

    {{-- Donut Member Status + Quick Stats --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col gap-4">
        <div>
            <h2 class="text-[13.5px] font-bold text-gray-700">Status Member</h2>
            <p class="text-[11px] text-gray-400 mt-0.5">Aktif vs Expired</p>
        </div>

        <div class="flex items-center justify-center gap-5">
            <div class="ring-chart-wrap">
                <canvas id="chartMember"></canvas>
                <div class="ring-label">
                    <span class="text-lg font-extrabold text-gray-800">{{ $totalMember }}</span>
                    <span class="text-[9.5px] text-gray-400 font-medium">Total</span>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
                    <div>
                        <div class="text-[11px] font-semibold text-gray-700">{{ $memberAktif }} Aktif</div>
                        <div class="text-[9.5px] text-gray-400">
                            {{ $totalMember > 0 ? round($memberAktif/$totalMember*100) : 0 }}% dari total
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-300 flex-shrink-0"></span>
                    <div>
                        <div class="text-[11px] font-semibold text-gray-700">{{ $memberExpired }} Expired</div>
                        <div class="text-[9.5px] text-gray-400">
                            {{ $totalMember > 0 ? round($memberExpired/$totalMember*100) : 0 }}% dari total
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-200 flex-shrink-0"></span>
                    <div>
                        <div class="text-[11px] font-semibold text-gray-700">{{ $memberLainnya }} Lainnya</div>
                        <div class="text-[9.5px] text-gray-400">Tidak aktif</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kunjungan sparkline 7 hari --}}
        <div class="mt-auto">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-semibold text-gray-600">Kunjungan 7 Hari</span>
                <span class="text-[10px] text-gray-400">Total: {{ array_sum($kunjungan7Hari) }}</span>
            </div>
            <div class="flex items-end gap-1 h-10">
                @foreach($kunjungan7Hari as $i => $val)
                    @php $max = max($kunjungan7Hari) ?: 1; $h = round($val/$max*100); @endphp
                    <div title="{{ $val }} kunjungan"
                         class="spark-bar {{ $i === count($kunjungan7Hari)-1 ? 'today' : '' }}"
                         style="height: {{ max($h,8) }}%;">
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-1">
                @foreach($label7Hari as $i => $lbl)
                    <span class="text-[8.5px] text-gray-300 {{ $i === count($label7Hari)-1 ? 'text-emerald-500 font-bold' : '' }}">
                        {{ $lbl }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ===== ROW 3: Transaksi Terbaru + Member Hampir Expired ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 section-fade">

    {{-- Transaksi Terbaru --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="text-[13.5px] font-bold text-gray-700">Transaksi Terbaru</h2>
            <a href="{{ route('transaksi.index') }}"
               class="text-[11px] text-emerald-500 font-semibold hover:text-emerald-600">
                Lihat semua →
            </a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($transaksiTerbaru as $trx)
            <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/60 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                    @if($trx->metode_pembayaran === 'transfer')
                        <i class="fa-solid fa-building-columns text-blue-400 text-[11px]"></i>
                    @elseif($trx->metode_pembayaran === 'qris')
                        <i class="fa-solid fa-qrcode text-violet-400 text-[11px]"></i>
                    @else
                        <i class="fa-solid fa-money-bills text-emerald-400 text-[11px]"></i>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[12px] font-semibold text-gray-700 truncate">
                        {{ $trx->member?->nama ?? $trx->nama_tamu ?? 'Tamu' }}
                    </div>
                    <div class="text-[10.5px] text-gray-400">
                        {{ $trx->kode_invoice }} &middot; {{ $trx->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-[12px] font-bold text-gray-800">
                        Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}
                    </div>
                    <span class="text-[9.5px] font-semibold px-1.5 py-0.5 rounded-full
                        {{ $trx->status === 'dibayar' ? 'bg-emerald-50 text-emerald-600'
                           : ($trx->status === 'pending' ? 'bg-orange-50 text-orange-500'
                           : 'bg-gray-100 text-gray-400') }}">
                        {{ ucfirst($trx->status) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-300 text-sm">
                <i class="fa-solid fa-receipt text-2xl mb-2 block"></i>
                Belum ada transaksi
            </div>
            @endforelse
        </div>
    </div>

    {{-- Member Hampir Expired --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <div>
                <h2 class="text-[13.5px] font-bold text-gray-700">Member Hampir Expired</h2>
                <p class="text-[10.5px] text-gray-400 mt-0.5">Dalam 7 hari ke depan</p>
            </div>
            <a href="{{ route('member.index') }}"
               class="text-[11px] text-emerald-500 font-semibold hover:text-emerald-600">
                Lihat semua →
            </a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($memberHampirExpired as $m)
            <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/60 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0">
                    {{ strtoupper(substr($m->nama, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[12px] font-semibold text-gray-700 truncate">{{ $m->nama }}</div>
                    <div class="text-[10.5px] text-gray-400">{{ $m->kode_member }}</div>
                </div>
                <div class="text-right flex-shrink-0">
                    @php
                        $sisa = now()->diffInDays($m->tanggal_kadaluarsa, false);
                    @endphp
                    <div class="text-[11px] font-bold
                        {{ $sisa <= 1 ? 'text-red-500' : ($sisa <= 3 ? 'text-orange-500' : 'text-yellow-500') }}">
                        {{ $sisa <= 0 ? 'Hari ini!' : $sisa . ' hari lagi' }}
                    </div>
                    <div class="text-[10px] text-gray-400 mt-0.5">
                        {{ $m->tanggal_kadaluarsa->format('d M Y') }}
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-300 text-sm">
                <i class="fa-solid fa-circle-check text-2xl mb-2 block text-emerald-200"></i>
                Tidak ada member yang akan expired
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// ── Chart Pendapatan ──────────────────────────────────────────
const ctxP = document.getElementById('chartPendapatan').getContext('2d');

const gradP = ctxP.createLinearGradient(0, 0, 0, 200);
gradP.addColorStop(0, 'rgba(16,185,129,0.18)');
gradP.addColorStop(1, 'rgba(16,185,129,0)');

new Chart(ctxP, {
    type: 'line',
    data: {
        labels: @json($label7Hari),
        datasets: [{
            data: @json($pendapatan7Hari),
            borderColor: '#10b981',
            borderWidth: 2,
            backgroundColor: gradP,
            pointBackgroundColor: '#10b981',
            pointRadius: 4,
            pointHoverRadius: 6,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: {
            callbacks: {
                label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
            },
            backgroundColor: '#0f172a',
            titleColor: '#94a3b8',
            bodyColor: '#f1f5f9',
            padding: 10,
            cornerRadius: 8,
        }},
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#9ca3af' }},
            y: {
                grid: { color: '#f3f4f6' },
                ticks: {
                    font: { size: 10 },
                    color: '#9ca3af',
                    callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k'
                }
            }
        }
    }
});

// ── Chart Donut Member ────────────────────────────────────────
const ctxM = document.getElementById('chartMember').getContext('2d');
new Chart(ctxM, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $memberAktif }}, {{ $memberExpired }}, {{ $memberLainnya }}],
            backgroundColor: ['#34d399', '#fca5a5', '#e5e7eb'],
            borderWidth: 0,
            hoverOffset: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: { legend: { display: false }, tooltip: { enabled: true } },
    }
});
</script>
@endpush