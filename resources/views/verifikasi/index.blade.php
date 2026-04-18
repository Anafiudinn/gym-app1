@extends('layouts.admin')
@section('title', 'Verifikasi Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')

@push('styles')
<style>
    /* Existing styles + improvements */
    .fade-up { animation: fadeUp 0.35s ease both; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .tab-btn {
        position: relative;
        padding: 11px 0;
        font-size: 12.5px;
        font-weight: 600;
        color: #6b7280;
        transition: color 0.15s;
        border-bottom: 2px solid transparent;
    }
    .tab-btn:hover { color: #374151; }
    .tab-btn.active { color: #10b981; border-bottom-color: #10b981; }
    
    .card { background: #fff; border-radius: 14px; border: 1px solid #f0f0f0; overflow: hidden; }
    .th { 
        padding: 11px 20px; text-align: left; font-size: 10px; font-weight: 800; 
        text-transform: uppercase; letter-spacing: 0.1em; color: #9ca3af; 
        background: #fafafa; border-bottom: 1px solid #f3f4f6;
    }
    .td { padding: 13px 20px; border-bottom: 1px solid #f9fafb; vertical-align: middle; }
    tr:last-child .td { border-bottom: none; }
    tr:hover td { background: #ecfdf5; }
    
    /* ✅ SWEETALERT2 Action Buttons */
    .action-group { display: flex; gap: 8px; align-items: center; }
    .btn-accept {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white; border: none; padding: 8px 16px; border-radius: 10px;
        font-size: 11.5px; font-weight: 600; cursor: pointer;
        box-shadow: 0 2px 8px rgba(16,185,129,0.3); transition: all 0.2s;
    }
    .btn-accept:hover { 
        background: linear-gradient(135deg, #059669, #047857); 
        transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16,185,129,0.4);
    }
    
    .btn-reject {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
        padding: 8px 12px; border-radius: 10px; font-size: 11.5px; font-weight: 600;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-reject:hover { 
        background: #dc2626; color: white; border-color: #dc2626;
        transform: translateY(-1px);
    }
    
    .reject-reason { 
        width: 140px; padding: 6px 10px; border: 1px solid #e5e7eb; 
        border-radius: 8px; font-size: 11px; outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .reject-reason:focus {
        border-color: #fca5a5; box-shadow: 0 0 0 3px rgba(220,38,38,0.1);
    }
    
    .status-badge { 
        padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .status-diterima { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .status-ditolak  { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    
    .counter-badge {
        background: #fef3c7; color: #d97706; font-size: 10px; font-weight: 800;
        padding: 2px 8px; border-radius: 12px; margin-left: 6px;
    }
    
    /* Filter improvements */
    .filter-bar {
        display: flex; align-items: center; gap: 12px; padding: 16px 20px;
        background: linear-gradient(135deg, #fafafa 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9; flex-wrap: wrap;
    }
    .filter-input, .filter-select, .filter-date {
        font-size: 12px; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 8px 12px; outline: none; color: #374151; background: white;
        transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .filter-input:focus, .filter-select:focus, .filter-date:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        transform: translateY(-1px);
    }
</style>
@endpush

@push('scripts')
<script>
let currentEditId = null;

/* ── Tab switching ── */
function switchTab(type) {
    ['pending', 'history'].forEach(t => {
        document.getElementById('tab-' + t).classList.toggle('active', t === type);
        document.getElementById('section-' + t).classList.toggle('hidden', t !== type);
    });
}

/* ── Filter Functions (existing) ── */
function filterPending() { /* existing code */ }
function resetPendingFilter() { /* existing code */ }
function filterHistory() { /* existing code */ }
function resetHistoryFilter() { /* existing code */ }

/* ✅ SWEETALERT2 - TERIMA PEMBAYARAN */
async function handleTerima(event, verifikasiId) {
    event.preventDefault();
    
    const confirmed = await GymProAlert.confirm(
        'Terima Pembayaran',
        'Member akan mendapatkan akses sesuai paket yang dibeli. Pastikan bukti pembayaran sudah benar!',
        '✅ Terima Pembayaran',
        'Batal'
    );
    
    if (confirmed) {
        event.target.closest('form').submit();
    }
}

/* ✅ SWEETALERT2 - TOLAK PEMBAYARAN */
async function handleTolak(event, verifikasiId, memberName) {
    event.preventDefault();
    
    const reason = event.target.closest('form').querySelector('input[name="catatan_admin"]').value.trim();
    
    if (!reason) {
        GymProAlert.error('Alasan Diperlukan', 'Mohon masukkan alasan penolakan!');
        return;
    }
    
    const confirmed = await GymProAlert.confirm(
        'Tolak Pembayaran',
        `Pembayaran member "${memberName}" akan ditolak dengan alasan:\n\n${reason}\n\nMember akan diberitahu via WhatsApp.`,
        '❌ Tolak Pembayaran',
        'Batal'
    );
    
    if (confirmed) {
        event.target.closest('form').submit();
    }
}
</script>
@endpush

@section('content')
@php $pending = $data->where('status', 'pending'); @endphp

{{-- TABS --}}
<div class="flex gap-6 mb-6 pb-4 border-b border-gray-100 fade-up">
    <button onclick="switchTab('pending')" id="tab-pending" class="tab-btn active">
        Perlu Verifikasi
        @if($pending->count() > 0)
            <span class="counter-badge">{{ $pending->count() }}</span>
        @endif
    </button>
    <button onclick="switchTab('history')" id="tab-history" class="tab-btn">
        Riwayat Selesai
    </button>
</div>

{{-- ============================================================ --}}
{{-- SECTION: PENDING - ✅ SWEETALERT2 INTEGRATED --}}
<div id="section-pending">
    <div class="card shadow-sm">
        {{-- Filter Bar --}}
        <div class="filter-bar">
            <div class="search-wrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="search-pending" class="filter-input" placeholder="Cari invoice / nama member..." oninput="filterPending()">
            </div>
            <button class="reset-filter-btn" onclick="resetPendingFilter()">
                <i class="fa-solid fa-rotate-left mr-1"></i>Reset
            </button>
            <span id="pending-count-info" class="filter-label ml-auto"></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr>
                        <th class="th">Invoice</th>
                        <th class="th">Member & Paket</th>
                        <th class="th text-center">No. WA</th>
                        <th class="th text-center">Transfer</th>
                        <th class="th text-center">Bukti</th>
                        <th class="th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-pending">
                    @forelse($data->where('status', 'pending') as $d)
                    <tr class="pending-row transition-all hover:shadow-sm" 
                        data-invoice="{{ strtolower($d->transaksi->kode_invoice ?? '') }}"
                        data-nama="{{ strtolower($d->transaksi->member->nama ?? '') }}">
                        
                        <td class="td font-mono">
                            <div class="text-sm font-bold text-gray-800">#{{ $d->transaksi->kode_invoice }}</div>
                            <div class="text-xs text-gray-400 mt-1">{{ $d->created_at->format('d M Y H:i') }}</div>
                        </td>
                        
                        <td class="td">
                            <div class="font-semibold text-gray-800">{{ $d->transaksi->member->nama }}</div>
                            <div class="text-emerald-600 font-bold text-xs uppercase mt-1 tracking-wide">
                                {{ $d->transaksi->paket->nama_paket }}
                            </div>
                        </td>
                        
                        <td class="td text-center font-mono text-sm font-semibold">
                            {{ $d->transaksi->member->no_wa }}
                        </td>
                        
                        <td class="td text-center">
                            <div class="text-lg font-black text-gray-900">Rp {{ number_format($d->transaksi->jumlah_bayar) }}</div>
                            <div class="text-xs text-gray-500 mt-1 flex items-center justify-center gap-1">
                                <i class="fa-solid fa-building-columns"></i>
                                {{ $d->nama_bank }}<br>
                                <span class="text-xs">a/n {{ $d->nama_rekening }}</span>
                            </div>
                        </td>
                        
                        <td class="td text-center">
                            <a href="{{ asset('storage/' . $d->bukti_pembayaran) }}" target="_blank" 
                               class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-2 rounded-lg text-sm font-semibold transition-all">
                                <i class="fa-solid fa-image"></i> Lihat Bukti
                            </a>
                        </td>
                        
                        <td class="td">
                            <div class="action-group justify-end">
                                {{-- TOLAK - SweetAlert2 ✅ --}}
                                <form method="POST" action="{{ route('verifikasi.tolak', $d->id) }}" 
                                      class="inline-flex items-center gap-2"
                                      onsubmit="handleTolak(event, {{ $d->id }}, '{{ $d->transaksi->member->nama }}')">
                                    @csrf
                                    <input type="text" name="catatan_admin" placeholder="Alasan tolak..." 
                                           class="reject-reason" required maxlength="100">
                                    <button type="submit" class="btn-reject" title="Tolak Pembayaran">
                                        <i class="fa-solid fa-xmark"></i> Tolak
                                    </button>
                                </form>
                                
                                {{-- TERIMA - SweetAlert2 ✅ --}}
                                <form method="POST" action="{{ route('verifikasi.terima', $d->id) }}" class="inline-block"
                                      onsubmit="handleTerima(event, {{ $d->id }})">
                                    @csrf
                                    <button type="submit" class="btn-accept" title="Terima Pembayaran">
                                        <i class="fa-solid fa-check"></i> Terima
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="td py-16 text-center">
                            <div class="flex flex-col items-center text-gray-300">
                                <i class="fa-solid fa-check-circle text-4xl mb-3 opacity-30"></i>
                                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Semua sudah terverifikasi!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SECTION: HISTORY --}}
{{-- ============================================================ --}}
<div id="section-history" class="hidden">
    <div class="card">
        {{-- Filter Bar --}}
        <div class="filter-bar">
            {{-- Search --}}
            <div class="search-wrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
                </svg>
                <input
                    type="text"
                    id="search-history"
                    class="filter-input"
                    placeholder="Cari invoice atau nama member..."
                    oninput="filterHistory()"
                >
            </div>

            {{-- Filter Status --}}
            <div class="flex items-center gap-2">
                <span class="filter-label">Status:</span>
                <select id="filter-status" class="filter-select" onchange="filterHistory()">
                    <option value="">Semua</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>

            {{-- Filter Tanggal --}}
            <div class="flex items-center gap-2">
                <span class="filter-label">Dari:</span>
                <input type="date" id="filter-date-from" class="filter-date" onchange="filterHistory()">
                <span class="filter-label">s/d:</span>
                <input type="date" id="filter-date-to" class="filter-date" onchange="filterHistory()">
            </div>

            <button class="reset-filter-btn" onclick="resetHistoryFilter()">Reset</button>
            <span id="history-count-info" class="filter-label"></span>
        </div>

        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="th">Member</th>
                    <th class="th text-center">Tanggal</th>
                    <th class="th text-center">Nominal</th>
                    <th class="th text-center">Channel</th>
                    <th class="th text-center">Status</th>
                    <th class="th text-right">Catatan Admin</th>
                </tr>
            </thead>
            <tbody id="tbody-history">
                @forelse($data->whereIn('status', ['ditolak', 'diterima']) as $d)
                <tr class="history-row"
                    data-invoice="{{ strtolower($d->transaksi?->kode_invoice ?? '') }}"
                    data-nama="{{ strtolower($d->transaksi?->member?->nama ?? '') }}"
                    data-status="{{ $d->status }}"
                    data-date="{{ $d->created_at->format('Y-m-d') }}">
                    
                    <td class="td">
                        <div class="text-[13px] font-semibold text-gray-800">
                            {{ $d->transaksi?->member?->nama ?? 'Bukan Member / Terhapus' }}
                        </div>
                        <div class="text-[10.5px] font-mono text-gray-400 mt-0.5">
                            #{{ $d->transaksi?->kode_invoice ?? 'N/A' }}
                        </div>
                    </td>

                    <td class="td text-center">
                        <div class="text-[13px] font-semibold text-gray-800">{{ $d->created_at->format('d M Y') }}</div>
                        <div class="text-[10.5px] text-gray-500 mt-0.5">{{ $d->created_at->format('H:i') }}</div>
                    </td>

                    <td class="td text-center">
                        <span class="text-[13px] font-bold text-gray-800">
                            Rp{{ number_format($d->transaksi?->jumlah_bayar ?? 0, 0, ',', '.') }}
                        </span>
                    </td>

                    <td class="td text-center">
                        <span class="text-[13px] font-semibold text-gray-800">
                            {{ $d->transaksi?->channel ?? '-' }}
                        </span>
                    </td>

                    <td class="td text-center">
                        @if($d->status === 'diterima')
                            <span class="status-badge status-diterima">Diterima</span>
                        @else
                            <span class="status-badge status-ditolak">Ditolak</span>
                        @endif
                    </td>

                    <td class="td text-right">
                        <span class="text-[12px] text-gray-400 italic">{{ $d->catatan_admin ?? '—' }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="td py-12 text-center text-[12px] text-gray-400 italic">Belum ada riwayat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Empty state saat filter tidak ada hasil --}}
        <div id="history-empty-filter" class="empty-filter" style="display:none;">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
            </svg>
            <p>Tidak ada hasil yang cocok</p>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
    /* ── Tab switching ── */
    function switchTab(type) {
        ['pending', 'history'].forEach(t => {
            document.getElementById('tab-' + t).classList.toggle('active', t === type);
            document.getElementById('section-' + t).classList.toggle('hidden', t !== type);
        });
    }

    /* ── Filter Pending ── */
    function filterPending() {
        const q = document.getElementById('search-pending').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.pending-row');
        let visible = 0;

        rows.forEach(row => {
            const invoice = row.dataset.invoice || '';
            const nama    = row.dataset.nama    || '';
            const match   = !q || invoice.includes(q) || nama.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        const info = document.getElementById('pending-count-info');
        const emptyEl = document.getElementById('pending-empty-filter');

        if (q) {
            info.textContent = visible + ' hasil ditemukan';
            emptyEl.style.display = visible === 0 ? 'block' : 'none';
        } else {
            info.textContent = '';
            emptyEl.style.display = 'none';
        }
    }

    function resetPendingFilter() {
        document.getElementById('search-pending').value = '';
        filterPending();
    }

    /* ── Filter History ── */
    function filterHistory() {
        const q          = document.getElementById('search-history').value.toLowerCase().trim();
        const status     = document.getElementById('filter-status').value;
        const dateFrom   = document.getElementById('filter-date-from').value;
        const dateTo     = document.getElementById('filter-date-to').value;
        const rows       = document.querySelectorAll('.history-row');
        let visible      = 0;

        rows.forEach(row => {
            const invoice   = row.dataset.invoice || '';
            const nama      = row.dataset.nama    || '';
            const rowStatus = row.dataset.status  || '';
            const rowDate   = row.dataset.date    || '';

            const matchQ      = !q      || invoice.includes(q) || nama.includes(q);
            const matchStatus = !status || rowStatus === status;
            const matchFrom   = !dateFrom || rowDate >= dateFrom;
            const matchTo     = !dateTo   || rowDate <= dateTo;

            const show = matchQ && matchStatus && matchFrom && matchTo;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const hasFilter = q || status || dateFrom || dateTo;
        const info      = document.getElementById('history-count-info');
        const emptyEl   = document.getElementById('history-empty-filter');

        if (hasFilter) {
            info.textContent = visible + ' hasil ditemukan';
            emptyEl.style.display = visible === 0 ? 'block' : 'none';
        } else {
            info.textContent = '';
            emptyEl.style.display = 'none';
        }
    }

    function resetHistoryFilter() {
        document.getElementById('search-history').value   = '';
        document.getElementById('filter-status').value    = '';
        document.getElementById('filter-date-from').value = '';
        document.getElementById('filter-date-to').value   = '';
        filterHistory();
    }
</script>
@endpush