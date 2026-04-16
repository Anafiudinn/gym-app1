@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')

@push('styles')
<style>
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
    .tab-btn.active {
        color: #10b981;
        border-bottom-color: #10b981;
    }
    .card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }
    .th {
        padding: 11px 20px;
        text-align: left;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9ca3af;
        background: #fafafa;
        border-bottom: 1px solid #f3f4f6;
    }
    .td {
        padding: 13px 20px;
        border-bottom: 1px solid #f9fafb;
        vertical-align: middle;
    }
    tr:last-child .td { border-bottom: none; }
    tr:hover td { background: #fafafa; }
    .action-btn-accept {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #10b981;
        color: #fff;
        border: none;
        padding: 7px 13px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
    }
    .action-btn-accept:hover { background: #059669; }
    .action-btn-accept:active { transform: scale(0.97); }
    .action-btn-reject {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 7px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
    }
    .action-btn-reject:hover {
        background: #dc2626;
        color: #fff;
        border-color: #dc2626;
    }
    .reject-input {
        font-size: 11.5px;
        border: 1px solid #e5e7eb;
        border-radius: 7px;
        padding: 6px 10px;
        width: 130px;
        outline: none;
        color: #374151;
        transition: border-color 0.15s;
    }
    .reject-input:focus {
        border-color: #fca5a5;
        box-shadow: 0 0 0 2px rgba(220,38,38,0.08);
    }
    .view-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        font-weight: 600;
        color: #10b981;
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        padding: 5px 11px;
        border-radius: 7px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .view-btn:hover { background: #d1fae5; }
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-diterima { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .status-ditolak  { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .counter-badge {
        display: inline-block;
        background: #fef3c7;
        color: #d97706;
        font-size: 10px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 20px;
        margin-left: 6px;
    }

    /* Filter Bar */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        background: #fafafa;
        border-bottom: 1px solid #f3f4f6;
        flex-wrap: wrap;
    }
    .filter-input {
        font-size: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 7px 11px 7px 32px;
        outline: none;
        color: #374151;
        background: #fff;
        transition: border-color 0.15s, box-shadow 0.15s;
        width: 220px;
    }
    .filter-input:focus {
        border-color: #6ee7b7;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.08);
    }
    .filter-select {
        font-size: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 7px 11px;
        outline: none;
        color: #374151;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.15s;
    }
    .filter-select:focus {
        border-color: #6ee7b7;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.08);
    }
    .filter-date {
        font-size: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 7px 11px;
        outline: none;
        color: #374151;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.15s;
    }
    .filter-date:focus {
        border-color: #6ee7b7;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.08);
    }
    .filter-label {
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        white-space: nowrap;
    }
    .search-wrap {
        position: relative;
    }
    .search-wrap svg {
        position: absolute;
        left: 9px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }
    .reset-filter-btn {
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        background: none;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 7px 11px;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .reset-filter-btn:hover {
        background: #f3f4f6;
        color: #6b7280;
    }
    .no-result-row { display: none; }
    .empty-filter {
        padding: 48px 20px;
        text-align: center;
    }
    .empty-filter svg { opacity: 0.2; margin: 0 auto 10px; display: block; }
    .empty-filter p { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #9ca3af; }
</style>
@endpush

@section('content')

@php $pending = $data->where('status', 'pending'); @endphp

{{-- TABS --}}
<div class="flex gap-6 mb-5 border-b border-gray-100">
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
{{-- SECTION: PENDING --}}
{{-- ============================================================ --}}
<div id="section-pending">
    <div class="card">
        {{-- Search Bar --}}
        <div class="filter-bar">
            <div class="search-wrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
                </svg>
                <input
                    type="text"
                    id="search-pending"
                    class="filter-input"
                    placeholder="Cari invoice atau nama member..."
                    oninput="filterPending()"
                >
            </div>
            <button class="reset-filter-btn" onclick="resetPendingFilter()">Reset</button>
            <span id="pending-count-info" class="filter-label"></span>
        </div>

        <table class="w-full">
            <thead>
                <tr>
                    <th class="th">Kode Invoice</th>
                    <th class="th text-center">Member &amp; Paket</th>
                    <th class="th text-center">Nomer WA</th>
                    <th class="th text-center">Detail Transfer</th>
                    <th class="th text-center">Bukti</th>
                    <th class="th text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-pending">
                @forelse($data->where('status', 'pending') as $d)
                <tr class="pending-row"
                    data-invoice="{{ strtolower($d->transaksi->kode_invoice ?? '') }}"
                    data-nama="{{ strtolower($d->transaksi->member->nama ?? '') }}">
                    <td class="td">
                        <div class="text-[13px] font-semibold text-gray-800">#{{ $d->transaksi->kode_invoice ?? '-' }}</div>
                        <div class="text-[10.5px] font-mono text-gray-400 mt-0.5">{{ $d->created_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="td">
                        <div class="text-[13px] font-semibold text-gray-800">{{ $d->transaksi->member->nama ?? '-' }}</div>
                        <div class="text-[10.5px] text-emerald-500 font-bold uppercase tracking-tight mt-0.5">
                            {{ $d->transaksi->paket->nama_paket ?? '-' }}
                        </div>
                    </td>
                    <td class="td text-center">
                        <div class="text-[13px] font-semibold text-gray-800">{{ $d->transaksi->member->no_wa ?? '-' }}</div>
                    </td>
                    <td class="td text-center">
                        <div class="text-[13px] font-bold text-gray-900">Rp{{ number_format($d->transaksi->jumlah_bayar, 0, ',', '.') }}</div>
                        <div class="text-[10.5px] text-gray-400 mt-0.5">{{ $d->nama_bank }} a/n {{ $d->nama_rekening }}</div>
                    </td>
                    <td class="td text-center">
                        <a href="{{ asset('storage/' . $d->bukti_pembayaran) }}" target="_blank" class="view-btn">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lihat Bukti
                        </a>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-2">
                            {{-- TOLAK --}}
                            <form method="POST" action="/verifikasi/{{ $d->id }}/tolak" class="flex items-center gap-2">
                                @csrf
                                <input type="text" name="catatan_admin" placeholder="Alasan..." class="reject-input">
                                <button type="submit" class="action-btn-reject" title="Tolak">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                            {{-- TERIMA --}}
                            <form method="POST" action="/verifikasi/{{ $d->id }}/terima">
                                @csrf
                                <button type="submit" class="action-btn-accept">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Terima
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="td py-16 text-center">
                        <div class="flex flex-col items-center opacity-30">
                            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-[11px] font-bold uppercase tracking-widest">Semua sudah terverifikasi</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Empty state saat filter tidak ada hasil --}}
        <div id="pending-empty-filter" class="empty-filter" style="display:none;">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
            </svg>
            <p>Tidak ada hasil yang cocok</p>
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

        <table class="w-full">
            <thead>
                <tr>
                    <th class="th">Member</th>
                    <th class="th text-center">Tanggal</th>
                    <th class="th text-center">Nominal</th>
                    <th class="th text-center">Status</th>
                    <th class="th text-right">Catatan Admin</th>
                </tr>
            </thead>
            <tbody id="tbody-history">
                @forelse($data->whereIn('status', ['ditolak', 'diterima']) as $d)
                <tr class="history-row"
                    data-invoice="{{ strtolower($d->transaksi->kode_invoice ?? '') }}"
                    data-nama="{{ strtolower($d->transaksi->member->nama ?? '') }}"
                    data-status="{{ $d->status }}"
                    data-date="{{ $d->created_at->format('Y-m-d') }}">
                    <td class="td">
                        <div class="text-[13px] font-semibold text-gray-800">{{ $d->transaksi->member->nama ?? '-' }}</div>
                        <div class="text-[10.5px] font-mono text-gray-400 mt-0.5">#{{ $d->transaksi->kode_invoice }}</div>
                    </td>
                    <td class="td text-center">
                        <div class="text-[13px] font-semibold text-gray-800">{{ $d->created_at->format('d M Y') }}</div>
                        <div class="text-[10.5px] text-gray-500 mt-0.5">{{ $d->created_at->format('H:i') }}</div>
                    </td>
                    <td class="td text-center">
                        <span class="text-[13px] font-bold text-gray-800">Rp{{ number_format($d->transaksi->jumlah_bayar, 0, ',', '.') }}</span>
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
                    <td colspan="5" class="td py-12 text-center text-[12px] text-gray-400 italic">Belum ada riwayat.</td>
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