@extends('layouts.admin')
@section('title', 'Verifikasi Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')

@push('styles')
<style>
    .fade-up { animation: fadeUp 0.3s ease both; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Tabs ── */
    .tab-btn {
        position: relative;
        padding: 10px 2px;
        font-size: 12.5px;
        font-weight: 600;
        color: #94a3b8;
        border-bottom: 2px solid transparent;
        transition: color 0.15s;
        white-space: nowrap;
    }
    .tab-btn:hover { color: #475569; }
    .tab-btn.active { color: #10b981; border-bottom-color: #10b981; }

    /* ── Status badges ── */
    .badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 99px;
        font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .badge-pending  { background: #fef9ec; color: #b45309; border: 1px solid #fde68a; }
    .badge-terima   { background: rgba(16,185,129,0.08); color: #059669; border: 1px solid rgba(16,185,129,0.2); }
    .badge-tolak    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

    /* ── Pending counter ── */
    .counter-pill {
        background: #f97316;
        color: #fff;
        font-size: 9px; font-weight: 800;
        padding: 2px 7px; border-radius: 99px; line-height: 1.4;
    }

    /* ── Filter bar ── */
    .filter-bar {
        display: flex; align-items: center; gap: 8px;
        padding: 12px 16px; background: #fafafa;
        border-bottom: 1px solid #f1f5f9; flex-wrap: wrap;
    }
    .filter-input, .filter-select, .filter-date {
        font-size: 12px; border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 7px 11px; outline: none; color: #374151; background: #fff;
        transition: border-color 0.15s, box-shadow 0.15s; font-family: inherit;
    }
    .filter-input:focus, .filter-select:focus, .filter-date:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.08);
    }
    .filter-input  { min-width: 200px; }
    .filter-select { min-width: 110px; }
    .filter-date   { min-width: 130px; }

    /* ── Table ── */
    .th-cell {
        padding: 10px 16px; text-align: left;
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.1em; color: #94a3b8;
        background: #f8fafc; border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .td-cell {
        padding: 12px 16px; border-bottom: 1px solid #f8fafc;
        vertical-align: middle; font-size: 12.5px; color: #374151;
    }
    tr:last-child .td-cell { border-bottom: none; }
    tbody tr:hover td { background: #f0fdf9; }

    /* ── Action buttons ── */
    .btn-terima {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(16,185,129,0.1); color: #059669;
        border: 1px solid rgba(16,185,129,0.2);
        padding: 7px 13px; border-radius: 8px;
        font-size: 11.5px; font-weight: 700; cursor: pointer;
        transition: all 0.15s; white-space: nowrap; font-family: inherit;
    }
    .btn-terima:hover { background: #10b981; color: #fff; border-color: #10b981; }

    .btn-tolak {
        display: inline-flex; align-items: center; gap: 5px;
        background: #fef2f2; color: #dc2626;
        border: 1px solid #fecaca;
        padding: 7px 13px; border-radius: 8px;
        font-size: 11.5px; font-weight: 700; cursor: pointer;
        transition: all 0.15s; white-space: nowrap; font-family: inherit;
    }
    .btn-tolak:hover { background: #dc2626; color: #fff; border-color: #dc2626; }

    .input-alasan {
        font-size: 11.5px; border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 7px 10px; outline: none; color: #374151; background: #fff;
        transition: border-color 0.15s; font-family: inherit; width: 140px;
    }
    .input-alasan:focus { border-color: #fca5a5; box-shadow: 0 0 0 3px rgba(220,38,38,0.08); }

    /* ── Empty state ── */
    .empty-state {
        display: flex; flex-direction: column; align-items: center;
        padding: 48px 20px; gap: 10px;
    }
    .empty-icon {
        width: 48px; height: 48px; border-radius: 14px;
        background: #f8fafc; border: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: center;
    }

    /* ── Mobile pending card ── */
    .pending-card {
        background: #fff; border: 1px solid #f1f5f9;
        border-radius: 12px; padding: 14px;
        transition: box-shadow 0.15s;
    }
    .pending-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

    /* ── Reset btn ── */
    .btn-reset {
        display: inline-flex; align-items: center; gap: 5px;
        background: #f1f5f9; color: #64748b; border: none;
        padding: 7px 12px; border-radius: 8px;
        font-size: 11.5px; font-weight: 600; cursor: pointer;
        transition: background 0.15s; font-family: inherit;
    }
    .btn-reset:hover { background: #e2e8f0; }
</style>
@endpush

@section('content')
@php
    $pending = $data->where('status', 'pending');
    $history = $data->whereIn('status', ['diterima', 'ditolak']);
@endphp

{{-- ── TABS ── --}}
<div class="flex items-center gap-6 mb-5 pb-3 border-b border-gray-100 fade-up overflow-x-auto">
    <button onclick="switchTab('pending')" id="tab-pending" class="tab-btn active flex items-center gap-2">
        Perlu Verifikasi
        @if($pending->count() > 0)
            <span class="counter-pill">{{ $pending->count() }}</span>
        @endif
    </button>
    <button onclick="switchTab('history')" id="tab-history" class="tab-btn">
        Riwayat Selesai
    </button>
</div>

{{-- ════════════════════════════════════════════
     SECTION: PENDING
════════════════════════════════════════════ --}}
<div id="section-pending" class="fade-up space-y-4">

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="filter-bar">
            <div class="relative flex-1" style="min-width:180px;">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-800 "></i>
                <input type="text" id="search-pending"
                    class="filter-input w-full pl-8"
                    placeholder="Cari invoice / nama member..."
                    oninput="filterPending()">
            </div>
            <button class="btn-reset" onclick="resetPendingFilter()">
                <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset
            </button>
            <span id="pending-count-info" class="text-[11px] text-gray-400 font-medium ml-auto"></span>
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[860px]">
                <thead>
                    <tr>
                        <th class="th-cell">Invoice</th>
                        <th class="th-cell">Member & Paket</th>
                        <th class="th-cell text-center">No. WA</th>
                        <th class="th-cell text-center">Transfer</th>
                        <th class="th-cell text-center">Bukti</th>
                        <th class="th-cell text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-pending">
                    @forelse($pending as $d)
                    <tr class="pending-row"
                        data-invoice="{{ strtolower($d->transaksi->kode_invoice ?? '') }}"
                        data-nama="{{ strtolower($d->transaksi->member->nama ?? '') }}">

                        <td class="td-cell">
                            <div class="font-mono font-bold text-gray-800 text-[12.5px]">#{{ $d->transaksi->kode_invoice }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $d->created_at->format('d M Y, H:i') }}</div>
                        </td>

                        <td class="td-cell">
                            <div class="font-semibold text-gray-800 text-[12.5px]">{{ $d->transaksi->member->nama }}</div>
                            <div class="text-[10.5px] font-bold text-emerald-600 mt-0.5 uppercase tracking-wide">{{ $d->transaksi->paket->nama_paket }}</div>
                        </td>

                        <td class="td-cell text-center font-jakarta text-[12px] font-semibold text-gray-700">
                            {{ $d->transaksi->member->no_wa }}
                        </td>

                        <td class="td-cell text-center">
                            <div class="font-black text-gray-900 text-[14px]">Rp {{ number_format($d->transaksi->jumlah_bayar) }}</div>
                            <div class="text-[10px] text-gray-400 mt-1">
                                <i class="fa-solid fa-building-columns mr-0.5"></i>{{ $d->nama_bank }}
                                &middot; {{ $d->nama_rekening }}
                            </div>
                        </td>

                       <td class="td-cell text-center">
    @if($d->bukti_pembayaran)
        {{-- GANTI asset() menjadi Storage::disk('s3')->url() --}}
       <a href="{{ Storage::url($d->bukti_pembayaran) }}" target="_blank"
           class="inline-flex items-center gap-1.5 text-[11.5px] font-semibold text-emerald-600 hover:text-emerald-700 px-3 py-1.5 rounded-lg transition"
           style="background:rgba(16,185,129,0.08);">
            <i class="fa-solid fa-image text-[10px]"></i> Lihat
        </a>
    @else
        <span class="text-gray-400 italic text-[11px]">Tidak ada bukti</span>
    @endif
</td>
                        <td class="td-cell">
                            <div class="flex items-center gap-2 justify-end flex-wrap">
                                {{-- TOLAK --}}
                                <form method="POST" action="{{ route('verifikasi.tolak', $d->id) }}"
                                      class="inline-flex items-center gap-1.5"
                                      onsubmit="handleTolak(event, '{{ $d->transaksi->member->nama }}')">
                                    @csrf
                                    <input type="text" name="catatan_admin" placeholder="Alasan..."
                                           class="input-alasan" required maxlength="100">
                                    <button type="submit" class="btn-tolak">
                                        <i class="fa-solid fa-xmark text-[10px]"></i> Tolak
                                    </button>
                                </form>

                                {{-- TERIMA --}}
                                <form method="POST" action="{{ route('verifikasi.terima', $d->id) }}"
                                      class="inline-block"
                                      onsubmit="handleTerima(event)">
                                    @csrf
                                    <button type="submit" class="btn-terima">
                                        <i class="fa-solid fa-check text-[10px]"></i> Terima
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
                                </div>
                                <p class="text-[12px] font-semibold text-gray-400">Semua sudah terverifikasi!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden p-3 space-y-3" id="pending-mobile-list">
            @forelse($pending as $d)
            <div class="pending-card pending-row"
                 data-invoice="{{ strtolower($d->transaksi->kode_invoice ?? '') }}"
                 data-nama="{{ strtolower($d->transaksi->member->nama ?? '') }}">

                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <div class="font-bold text-gray-800 text-[13px]">{{ $d->transaksi->member->nama }}</div>
                        <div class="font-mono text-[10px] text-gray-400 mt-0.5">#{{ $d->transaksi->kode_invoice }}</div>
                    </div>
                    <div>
                        <div class="font-black text-gray-900 text-[15px] text-right">Rp {{ number_format($d->transaksi->jumlah_bayar) }}</div>
                        <div class="text-[9.5px] text-emerald-600 font-bold uppercase tracking-wide text-right mt-0.5">{{ $d->transaksi->paket->nama_paket }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-[11px] mb-3 pt-3 border-t border-gray-50">
                    <div><span class="text-gray-400">No. WA</span><br><span class="font-mono font-semibold text-gray-700">{{ $d->transaksi->member->no_wa }}</span></div>
                    <div><span class="text-gray-400">Bank</span><br><span class="font-semibold text-gray-700">{{ $d->nama_bank }} · {{ $d->nama_rekening }}</span></div>
                    <div><span class="text-gray-400">Tanggal</span><br><span class="font-semibold text-gray-700">{{ $d->created_at->format('d M Y') }}</span></div>
                   <div>
    <span class="text-gray-400">Bukti</span><br>
    {{-- GANTI asset() menjadi Storage::disk('s3')->url() --}}
   <a href="{{ Storage::url($d->bukti_pembayaran) }}" target="_blank"
       class="font-semibold text-emerald-600 underline">Lihat foto</a>
</div>
                </div>

                {{-- Mobile actions --}}
                <div class="pt-3 border-t border-gray-50 space-y-2">
                    <form method="POST" action="{{ route('verifikasi.terima', $d->id) }}"
                          onsubmit="handleTerima(event)">
                        @csrf
                        <button type="submit" class="btn-terima w-full justify-center">
                            <i class="fa-solid fa-check text-[10px]"></i> Terima Pembayaran
                        </button>
                    </form>
                    <form method="POST" action="{{ route('verifikasi.tolak', $d->id) }}"
                          onsubmit="handleTolak(event, '{{ $d->transaksi->member->nama }}')">
                        @csrf
                        <input type="text" name="catatan_admin" placeholder="Alasan penolakan..."
                               class="input-alasan w-full mb-2" required maxlength="100">
                        <button type="submit" class="btn-tolak w-full justify-center">
                            <i class="fa-solid fa-xmark text-[10px]"></i> Tolak
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
                </div>
                <p class="text-[12px] font-semibold text-gray-400">Semua sudah terverifikasi!</p>
            </div>
            @endforelse
        </div>

        {{-- Filter empty --}}
        <div id="pending-empty-filter" class="hidden">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-magnifying-glass text-gray-300 text-xl"></i>
                </div>
                <p class="text-[12px] font-semibold text-gray-400">Tidak ada hasil yang cocok</p>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════
     SECTION: HISTORY
════════════════════════════════════════════ --}}
<div id="section-history" class="hidden fade-up space-y-4">

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="filter-bar gap-2">
            <div class="relative" style="min-width:180px; flex:1;">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
                <input type="text" id="search-history"
                    class="filter-input w-full pl-8"
                    placeholder="Cari invoice / nama..."
                    oninput="filterHistory()">
            </div>

            <select id="filter-status" class="filter-select" onchange="filterHistory()">
                <option value="">Semua Status</option>
                <option value="diterima">Diterima</option>
                <option value="ditolak">Ditolak</option>
            </select>

            <input type="date" id="filter-date-from" class="filter-date" onchange="filterHistory()" title="Dari tanggal">
            <span class="text-[11px] text-gray-400 font-medium hidden sm:block">s/d</span>
            <input type="date" id="filter-date-to" class="filter-date" onchange="filterHistory()" title="Sampai tanggal">

            <button class="btn-reset" onclick="resetHistoryFilter()">
                <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset
            </button>
            <span id="history-count-info" class="text-[11px] text-gray-400 font-medium ml-auto hidden sm:inline"></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
                <thead>
                    <tr>
                        <th class="th-cell">Member</th>
                        <th class="th-cell text-center">No Wa</th>
                        <th class="th-cell text-center">Tanggal</th>
                        <th class="th-cell text-center">Nominal</th>
                        <th class="th-cell text-center hidden sm:table-cell">Channel</th>
                        <th class="th-cell text-center">Status</th>
                        <th class="th-cell text-center">Bukti</th>
                        <th class="th-cell text-right hidden md:table-cell">Catatan</th>
                    </tr>
                </thead>
                <tbody id="tbody-history">
                    @forelse($history as $d)
                    <tr class="history-row"
                        data-invoice="{{ strtolower($d->transaksi?->kode_invoice ?? '') }}"
                        data-nama="{{ strtolower($d->transaksi?->member?->nama ?? '') }}"
                        data-status="{{ $d->status }}"
                        data-date="{{ $d->created_at->format('Y-m-d') }}">

                        <td class="td-cell">
                            <div class="font-semibold text-gray-800 text-[12.5px]">
                                {{ $d->transaksi?->member?->nama ?? 'Member terhapus' }}
                            </div>
                            <div class="font-mono text-[10px] text-gray-400 mt-0.5">
                                #{{ $d->transaksi?->kode_invoice ?? 'N/A' }}
                            </div>
                        </td>
                            <td class="td-cell text-center font-semibold text-[12px] font-semibold text-gray-700">
                                {{ $d->transaksi?->member?->no_wa ?? '—' }}
                            </td>

                        <td class="td-cell text-center">
                            <div class="font-semibold text-gray-800 text-[12px]">{{ $d->created_at->format('d M Y') }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $d->created_at->format('H:i') }}</div>
                        </td>

                        <td class="td-cell text-center">
                            <span class="font-bold text-gray-800 text-[12.5px]">
                                Rp {{ number_format($d->transaksi?->jumlah_bayar ?? 0, 0, ',', '.') }}
                            </span>
                        </td>

                        <td class="td-cell text-center font-semibold text-gray-700 text-[12px] hidden sm:table-cell">
                            {{ $d->transaksi?->channel ?? '—' }}
                        </td>

                        <td class="td-cell text-center">
                            @if($d->status === 'diterima')
                                <span class="badge badge-terima">
                                    <i class="fa-solid fa-check text-[8px]"></i> Diterima
                                </span>
                            @else
                                <span class="badge badge-tolak">
                                    <i class="fa-solid fa-xmark text-[8px]"></i> Ditolak
                                </span>
                            @endif
                        </td>
                      <td class="td-cell text-center">
    @if($d->bukti_pembayaran)
        {{-- GANTI asset() menjadi Storage::disk('s3')->url() --}}
       <a href="{{ Storage::url($d->bukti_pembayaran) }}" target="_blank"
           class="inline-flex items-center gap-1.5 text-[11.5px] font-semibold text-emerald-600 hover:text-emerald-700 px-3 py-1.5 rounded-lg transition"
           style="background:rgba(16,185,129,0.08);">
            <i class="fa-solid fa-image text-[10px]"></i> Lihat
        </a>
    @else
        <span class="text-gray-400 italic text-[11px]">Tidak ada bukti</span>
    @endif
</td>

                        <td class="td-cell text-right text-[11.5px] text-gray-400 italic hidden md:table-cell">
                            {{ $d->catatan_admin ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fa-solid fa-clock-rotate-left text-gray-300 text-xl"></i>
                                </div>
                                <p class="text-[12px] font-semibold text-gray-400">Belum ada riwayat verifikasi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="history-empty-filter" class="hidden">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-magnifying-glass text-gray-300 text-xl"></i>
                </div>
                <p class="text-[12px] font-semibold text-gray-400">Tidak ada hasil yang cocok</p>
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

    /* ── Confirm terima ── */
    async function handleTerima(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        Swal.confirm({
            title: 'Terima Pembayaran?',
            text: 'Member akan mendapatkan akses sesuai paket yang dibeli.',
            confirmText: 'Ya, Terima',
            onConfirm: () => form.submit()
        });
    }

    /* ── Confirm tolak ── */
    async function handleTolak(event, memberName) {
        event.preventDefault();
        const form = event.target.closest('form');
        const reason = form.querySelector('input[name="catatan_admin"]').value.trim();

        if (!reason) {
            Swal.warning('Mohon masukkan alasan penolakan terlebih dahulu.', 'Alasan Diperlukan');
            return;
        }

        Swal.confirm({
            title: 'Tolak Pembayaran?',
            text: `Pembayaran ${memberName} akan ditolak. Alasan: "${reason}"`,
            confirmText: 'Ya, Tolak',
            onConfirm: () => form.submit()
        });
    }

    /* ── Filter Pending ── */
    function filterPending() {
        const q = document.getElementById('search-pending').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.pending-row');
        let visible = 0;

        rows.forEach(row => {
            const match = !q || (row.dataset.invoice || '').includes(q) || (row.dataset.nama || '').includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        const info = document.getElementById('pending-count-info');
        const emptyEl = document.getElementById('pending-empty-filter');
        info.textContent = q ? visible + ' hasil' : '';
        emptyEl.classList.toggle('hidden', !q || visible > 0);
    }

    function resetPendingFilter() {
        document.getElementById('search-pending').value = '';
        filterPending();
    }

    /* ── Filter History ── */
    function filterHistory() {
        const q        = document.getElementById('search-history').value.toLowerCase().trim();
        const status   = document.getElementById('filter-status').value;
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo   = document.getElementById('filter-date-to').value;
        const rows     = document.querySelectorAll('.history-row');
        let visible    = 0;

        rows.forEach(row => {
            const matchQ    = !q      || (row.dataset.invoice||'').includes(q) || (row.dataset.nama||'').includes(q);
            const matchSt   = !status || row.dataset.status === status;
            const matchFrom = !dateFrom || row.dataset.date >= dateFrom;
            const matchTo   = !dateTo   || row.dataset.date <= dateTo;
            const show = matchQ && matchSt && matchFrom && matchTo;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const hasFilter = q || status || dateFrom || dateTo;
        const info = document.getElementById('history-count-info');
        const emptyEl = document.getElementById('history-empty-filter');
        info.textContent = hasFilter ? visible + ' hasil' : '';
        emptyEl.classList.toggle('hidden', !hasFilter || visible > 0);
    }

    function resetHistoryFilter() {
        ['search-history','filter-status','filter-date-from','filter-date-to'].forEach(id => {
            document.getElementById(id).value = '';
        });
        filterHistory();
    }
</script>
@endpush