@extends('layouts.admin')
@section('title', 'Manajemen Member')
@section('page-title', 'Manajemen Member')

@push('styles')
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .fade-up   { animation: fadeUp 0.32s ease both; }
    .delay-1   { animation-delay: .04s; }
    .delay-2   { animation-delay: .08s; }
    .delay-3   { animation-delay: .13s; }
    .delay-4   { animation-delay: .18s; }

    /* Stat card */
    .stat-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eaecf4;
        box-shadow: 0 1px 4px rgba(30,33,57,.04);
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: box-shadow .18s, transform .18s;
    }
    .stat-card:hover {
        box-shadow: 0 6px 20px rgba(30,33,57,.09);
        transform: translateY(-2px);
    }
    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .stat-icon.indigo { background: #eef2ff; color: #6366f1; }
    .stat-icon.green  { background: #f0fdf4; color: #22c55e; }
    .stat-icon.amber  { background: #fffbeb; color: #f59e0b; }
    .stat-num   { font-size: 26px; font-weight: 800; color: #1e2139; line-height: 1; }
    .stat-label { font-size: 11px; font-weight: 600; color: #a0a3b8; margin-top: 4px; text-transform: uppercase; letter-spacing: .06em; }

    /* Search / filter bar */
    .filter-bar {
        background: #fff;
        border: 1px solid #eaecf4;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 1px 4px rgba(30,33,57,.04);
    }
    .filter-input {
        background: #f5f6fa;
        border: 1px solid #eaecf4;
        border-radius: 8px;
        padding: 8px 12px 8px 34px;
        font-size: 13px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1e2139;
        outline: none;
        transition: border-color .15s, background .15s;
        width: 100%;
    }
    .filter-input:focus { border-color: #6366f1; background: #fff; }
    .filter-input::placeholder { color: #b0b3c8; }

    .filter-select {
        background: #f5f6fa;
        border: 1px solid #eaecf4;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #4b5066;
        outline: none;
        transition: border-color .15s;
        cursor: pointer;
    }
    .filter-select:focus { border-color: #6366f1; }

    /* Table card */
    .table-card {
        background: #fff;
        border: 1px solid #eaecf4;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(30,33,57,.04);
        overflow: hidden;
    }
    .table-card table { width: 100%; border-collapse: collapse; }
    .table-card thead th {
        padding: 11px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #8b8fa8;
        text-transform: uppercase;
        letter-spacing: .07em;
        border-bottom: 1px solid #f0f2f9;
        background: #fafbff;
        white-space: nowrap;
    }
    .table-card tbody tr { border-bottom: 1px solid #f5f6fa; transition: background .12s; }
    .table-card tbody tr:last-child { border-bottom: none; }
    .table-card tbody tr:hover { background: #f8f9fe; }
    .table-card tbody td { padding: 12px 16px; font-size: 13px; color: #3d4166; vertical-align: middle; }

    /* Member avatar */
    .m-avatar {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
    }

    /* Badge */
    .badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
        white-space: nowrap;
    }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-aktif   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-expired { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .badge-nonaktif{ background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* Code chip */
    .code-chip {
        font-family: 'Courier New', monospace;
        font-size: 11px; font-weight: 600;
        background: #f5f6fa; border: 1px solid #eaecf4;
        color: #4b5066; padding: 3px 8px; border-radius: 6px;
    }

    /* Paket chip */
    .paket-chip {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 600;
        background: #eef2ff; border: 1px solid #c7d2fe;
        color: #4338ca; padding: 3px 10px; border-radius: 20px;
    }

    /* Action btn */
    .act-btn {
        width: 30px; height: 30px; border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; cursor: pointer; border: none;
        background: transparent; color: #a0a3b8;
        transition: all .14s; text-decoration: none;
    }
    .act-btn:hover        { background: #eef2ff; color: #6366f1; }
    .act-btn.toggle:hover { background: #f0fdf4; color: #22c55e; }
    .act-btn.del:hover    { background: #fef2f2; color: #ef4444; }

    /* Btn primary */
    .btn-primary {
        display: inline-flex; align-items: center; gap: 6px;
        background: #6366f1; color: #fff;
        border: none; border-radius: 8px;
        padding: 8px 16px; font-size: 13px; font-weight: 600;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer; transition: all .15s;
    }
    .btn-primary:hover { background: #4f46e5; box-shadow: 0 4px 12px rgba(99,102,241,.3); }

    .btn-outline {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fff; color: #4b5066;
        border: 1px solid #eaecf4; border-radius: 8px;
        padding: 8px 14px; font-size: 13px; font-weight: 600;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer; transition: all .15s; text-decoration: none;
    }
    .btn-outline:hover { background: #f5f6fa; border-color: #d1d5f0; }

    .btn-reset {
        display: inline-flex; align-items: center; justify-content: center;
        width: 36px; height: 36px; border-radius: 8px;
        background: #f5f6fa; border: 1px solid #eaecf4;
        color: #8b8fa8; text-decoration: none;
        transition: all .15s;
    }
    .btn-reset:hover { background: #eef2ff; color: #6366f1; border-color: #c7d2fe; }

    /* Pagination overrides */
    .pagination { display: flex; gap: 4px; }
    nav[aria-label="pagination"] span[aria-current="page"] span,
    nav[aria-label="pagination"] .page-item.active .page-link {
        background: #6366f1 !important;
        border-color: #6366f1 !important;
        color: #fff !important;
        border-radius: 7px !important;
        padding: 5px 11px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
    }

    /* Row hover reveal */
    .row-actions { opacity: 0.4; transition: opacity .15s; }
    tr:hover .row-actions { opacity: 1; }
</style>
@endpush

@push('scripts')
<script>
async function handleToggleMember(event, memberId, currentStatus) {
    event.preventDefault();
    const action    = currentStatus === 'aktif' ? 'nonaktifkan' : 'aktifkan';
    const newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
    const confirmed = await GymProAlert.confirm(
        'Ubah Status Member',
        `Apakah Anda yakin ingin ${action} member ini?`,
        `${action.charAt(0).toUpperCase() + action.slice(1)} Member`,
        'Batal'
    );
    if (confirmed) {
        event.target.closest('form').querySelector('input[name="status"]').value = newStatus;
        event.target.closest('form').submit();
    }
}
</script>
@endpush

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

    {{-- ── STAT CARDS ── --}}
    <div class="fade-up delay-1" style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px;">

        <div class="stat-card">
            <div class="stat-icon indigo"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-num">{{ $stats['totalMembers'] }}</div>
                <div class="stat-label">Total Member</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
            <div>
                <div class="stat-num" style="color:#15803d;">{{ $stats['activeMembers'] }}</div>
                <div class="stat-label">Member Aktif</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>
            <div>
                <div class="stat-num" style="color:#b45309;">{{ $stats['expiredMembers'] }}</div>
                <div class="stat-label">Expired</div>
            </div>
        </div>

    </div>

    {{-- ── FILTER BAR ── --}}
    <div class="filter-bar fade-up delay-2">
        <form method="GET" action="{{ route('member.index') }}"
              style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">

            <div style="position:relative; flex:1; min-width:200px;">
                <i class="fa-solid fa-magnifying-glass"
                   style="position:absolute; left:11px; top:50%; transform:translateY(-50%); font-size:11px; color:#b0b3c8;"></i>
                <input class="filter-input" type="text" name="search"
                       value="{{ request('search') }}" placeholder="Cari nama atau kode member…">
            </div>

            <select class="filter-select" name="status">
                <option value="">Semua Status</option>
                <option value="aktif"    {{ request('status')==='aktif'    ? 'selected':'' }}>Aktif</option>
                <option value="expired"  {{ request('status')==='expired'  ? 'selected':'' }}>Expired</option>
                <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected':'' }}>Nonaktif</option>
            </select>

            <button type="submit" class="btn-primary" style="height:36px;">
                <i class="fa-solid fa-filter" style="font-size:11px;"></i> Filter
            </button>

            @if(request('search') || request('status'))
            <a href="{{ route('member.index') }}" class="btn-reset" title="Reset">
                <i class="fa-solid fa-rotate-left" style="font-size:12px;"></i>
            </a>
            @endif

        </form>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="table-card fade-up delay-3">

        {{-- Card Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:14px 20px; border-bottom:1px solid #f0f2f9;">
            <span style="font-size:15px; font-weight:700; color:#1e2139;">Daftar Member</span>
        </div>

        <div style="overflow-x:auto;">
        <table style="min-width:820px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Kode</th>
                    <th>Telepon</th>
                    <th>Paket</th>
                    <th>Berlaku s/d</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($members as $member)
            @php
                $isExpired   = $member->tanggal_kadaluarsa && \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->isPast();
                $statusLabel = $member->status; // 'aktif' | 'expired' | 'nonaktif'
                $avatarColors = [
                    '#6366f1','#8b5cf6','#ec4899','#f43f5e',
                    '#f59e0b','#10b981','#06b6d4','#3b82f6'
                ];
                $avatarBg = $avatarColors[ord(strtoupper($member->nama[0])) % count($avatarColors)];
            @endphp
            <tr>
                {{-- No --}}
                <td style="font-weight:600; color:#8b8fa8; font-size:12px; width:40px;">
                    {{ $loop->iteration }}
                </td>

                {{-- Member --}}
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="m-avatar" style="background:{{ $avatarBg }};">
                            {{ strtoupper(substr($member->nama, 0, 2)) }}
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:600; color:#1e2139; line-height:1.2; white-space:nowrap;">
                                {{ $member->nama }}
                            </div>
                            @if($member->email)
                            <div style="font-size:11px; color:#a0a3b8; margin-top:2px; white-space:nowrap;">
                                {{ $member->email }}
                            </div>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Kode --}}
                <td><span class="code-chip">{{ $member->kode_member }}</span></td>

                {{-- Telepon --}}
                <td style="color:#6b7280; white-space:nowrap;">{{ $member->no_wa ?? '-' }}</td>

                {{-- Paket --}}
                <td>
                    @if($member->membership?->paket?->nama_paket)
                        <span class="paket-chip">
                            <i class="fa-solid fa-box" style="font-size:9px; opacity:.7;"></i>
                            {{ Str::limit($member->membership->paket->nama_paket, 14) }}
                        </span>
                    @else
                        <span style="font-size:12px; color:#c8cad8; font-style:italic;">—</span>
                    @endif
                </td>

                {{-- Berlaku s/d --}}
                <td style="white-space:nowrap;">
                    @if($member->tanggal_kadaluarsa)
                        <div style="display:flex; align-items:center; gap:5px;">
                            <i class="fa-regular fa-calendar"
                               style="font-size:10px; color:{{ $isExpired ? '#f59e0b' : '#c8cad8' }};"></i>
                            <span style="font-size:12px; color:{{ $isExpired ? '#b45309' : '#6b7280' }}; font-weight:{{ $isExpired ? '600' : '400' }};">
                                {{ \Carbon\Carbon::parse($member->tanggal_kadaluarsa)->format('d M Y') }}
                            </span>
                        </div>
                    @else
                        <span style="color:#c8cad8; font-size:12px;">—</span>
                    @endif
                </td>

                {{-- Status --}}
                <td style="text-align:center;">
                    @if($member->status === 'aktif')
                        <span class="badge badge-aktif">
                            <span class="badge-dot" style="background:#22c55e;"></span> Aktif
                        </span>
                    @elseif($member->status === 'expired')
                        <span class="badge badge-expired">
                            <span class="badge-dot" style="background:#f59e0b;"></span> Expired
                        </span>
                    @else
                        <span class="badge badge-nonaktif">
                            <span class="badge-dot" style="background:#94a3b8;"></span> Nonaktif
                        </span>
                    @endif
                </td>

                {{-- Aksi --}}
                <td style="text-align:center;">
                    <div class="row-actions" style="display:inline-flex; align-items:center; gap:3px;">

                        {{-- Detail --}}
                        <a href="{{ route('member.show', $member->id) }}"
                           class="act-btn" title="Lihat Detail">
                            <i class="fa-regular fa-eye"></i>
                        </a>

                        {{-- Toggle Status --}}
                        <form method="POST" action="{{ route('member.toggle', $member->id) }}"
                              style="display:inline;">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="">
                            <button type="submit"
                                onclick="handleToggleMember(event, {{ $member->id }}, '{{ $statusLabel }}')"
                                class="act-btn toggle"
                                title="{{ $member->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                @if($member->status === 'aktif')
                                    <i class="fa-solid fa-toggle-on" style="color:#22c55e; font-size:14px;"></i>
                                @else
                                    <i class="fa-solid fa-toggle-off" style="font-size:14px;"></i>
                                @endif
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding:56px 20px; text-align:center;">
                    <i class="fa-solid fa-users" style="font-size:36px; color:#e2e4f0;"></i>
                    <p style="font-size:13px; color:#a0a3b8; margin-top:10px; font-weight:500;">
                        Belum ada data member ditemukan
                    </p>
                    @if(request('search') || request('status'))
                    <a href="{{ route('member.index') }}" class="btn-primary"
                       style="margin-top:12px; display:inline-flex;">
                        <i class="fa-solid fa-rotate-left" style="font-size:11px;"></i> Reset Filter
                    </a>
                    @endif
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        @if($members->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f0f2f9; background:#fafbff;
                    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
            <p style="font-size:12px; color:#a0a3b8;">
                Menampilkan
                <strong style="color:#4b5066;">{{ $members->firstItem() }}</strong> –
                <strong style="color:#4b5066;">{{ $members->lastItem() }}</strong>
                dari <strong style="color:#4b5066;">{{ $members->total() }}</strong> member
            </p>
            {{ $members->appends(request()->query())->links() }}
        </div>
        @endif

    </div>{{-- /table-card --}}

</div>
@endsection