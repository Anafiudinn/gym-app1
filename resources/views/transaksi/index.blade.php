@extends('layouts.admin')
@section('title', 'Transaksi')
@section('page-title', 'Transaksi')

@push('styles')
<style>
    .tab-btn {
        padding: 10px 0;
        font-size: 12.5px;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 2px solid transparent;
        transition: all 0.15s;
    }

    .tab-btn:hover {
        color: #374151;
    }

    .tab-btn.active {
        color: #10b981;
        border-bottom-color: #10b981;
    }

    .form-input {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 9px 13px;
        font-size: 13px;
        color: #111827;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .form-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .form-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #9ca3af;
        margin-bottom: 5px;
    }

    .btn-green {
        width: 100%;
        background: #10b981;
        color: #fff;
        font-size: 12.5px;
        font-weight: 700;
        padding: 11px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
        letter-spacing: 0.04em;
    }

    .btn-green:hover {
        background: #059669;
    }

    .btn-green:active {
        transform: scale(0.985);
    }

    .btn-outline-green {
        width: 100%;
        background: #f0fdf4;
        color: #059669;
        font-size: 12.5px;
        font-weight: 700;
        padding: 11px;
        border-radius: 10px;
        border: 1px solid #bbf7d0;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-outline-green:hover {
        background: #dcfce7;
    }
</style>
@endpush

@section('content')
{{-- Tambahkan ini di atas form membership di index.blade.php --}}
@if ($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
{{-- MAIN GRID 2 KOLOM --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    {{-- KIRI: SUMMARY + FORM (4 kolom) --}}
    <div class="lg:col-span-4 space-y-4 order-1">

        {{-- SUMMARY CARDS - KIRI ATAS --}}
        <div class="bg-white rounded-xl border border-gray-100 px-5 py-4">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Pemasukan Hari Ini</p>
            <p class="text-[22px] font-bold text-gray-800 leading-none">Rp{{ number_format($totalHariIni, 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">{{ $countHariIni }} transaksi</p>
        </div>

        {{-- FORM INPUT - KIRI BAWAH --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="flex gap-5 px-5 border-b border-gray-100">
                <button onclick="switchTab('tamu')" id="btn-tamu" class="tab-btn active">Tamu Harian</button>
                <button onclick="switchTab('membership')" id="btn-membership" class="tab-btn">Membership</button>
            </div>

            <div class="p-5">
                {{-- PANE TAMU --}}
                <div id="pane-tamu">
                    <div class="flex items-center justify-between bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3 mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Paket Default</p>
                            <p class="text-[14px] font-bold text-emerald-900 mt-0.5">
                                {{ $paketDefault->nama_paket ?? ' Harian' }}
                            </p>
                        </div>
                        <span class="text-[20px] font-black text-emerald-600">
                            Rp{{ number_format($paketDefault->harga, 0, ',', '.') }}
                        </span>
                    </div>
                
                {{-- Cari bagian PANE TAMU di file Blade --}}
                <form method="POST" action="{{ route('transaksi.harian') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="form-label">Nama Pengunjung</label>
                        <input type="text" name="nama_tamu" placeholder="Masukkan nama tamu..." class="form-input" required>
                    </div>
                    <button type="submit" class="btn-green">Bayar Sekarang</button>
                </form>
            </div>

            {{-- PANE MEMBERSHIP --}}
            <div id="pane-membership" class="hidden">
                <form method="POST" action="/transaksi/membership" class="space-y-3">
                    @csrf
                    <div>
                        <label class="form-label">Tipe Transaksi</label>
                        <select name="tipe_member" id="tipe_member" class="form-input" required>
                            <option value="baru">Member Baru</option>
                            <option value="perpanjang">Perpanjang Member</option>
                        </select>
                    </div>

                    <div id="form_baru" class="space-y-2.5 p-3.5 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <input type="text" name="nama" placeholder="Nama Lengkap" class="form-input">
                        <input type="text" name="no_wa" placeholder="No. WhatsApp (628...)" class="form-input">
                        <select name="jenis_kelamin" class="form-input">
                            <option value="">Jenis Kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div id="form_perpanjang" class="hidden">
                        <label class="form-label">Pilih Member</label>
                        <select name="member_id" id="select-member" class="form-input">
                            <option value="">— Cari Nama / Kode —</option>
                            @foreach($members as $m)
                            <option value="{{ $m->id }}"
                                {{ isset($selectedMemberId) && $selectedMemberId == $m->id ? 'selected' : '' }}>
                                {{ $m->nama }} ({{ $m->kode_member }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Paket</label>
                        <select name="paket_id" class="form-input" required>
                            <option value="">— Pilih Durasi Paket —</option>
                            @foreach($paket as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_paket }} — Rp{{ number_format($p->harga, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn-outline-green">Proses Membership</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- KANAN: RIWAYAT TRANSAKSI (8 kolom) --}}
<div class="lg:col-span-8 space-y-3 order-2">

    {{-- FILTER BAR --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <form action="{{ route('transaksi.index') }}" method="GET">
            @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}"> @endif
            <div class="flex flex-wrap gap-2.5">
                <div class="flex-1 min-w-[160px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama / invoice..."
                        class="form-input text-[12px]" style="padding: 8px 12px;">
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="form-input text-[12px]" style="padding: 8px 12px; width: 138px;">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="form-input text-[12px]" style="padding: 8px 12px; width: 138px;">
                </div>
                <div>
                    <select name="tipe" class="form-input text-[12px]" style="padding: 8px 12px; width: 130px;">
                        <option value="">Semua Tipe</option>
                        <option value="harian" {{ request('tipe') === 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="membership" {{ request('tipe') === 'membership' ? 'selected' : '' }}>Membership</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="form-input text-[12px]" style="padding: 8px 12px; width: 130px;">
                        <option value="">Semua Status</option>
                        <option value="dibayar" {{ request('status') === 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <button type="submit"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold px-4 py-2 rounded-lg transition">
                    <i class="fa-solid fa-filter text-[10px] mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search','date_from','date_to','tipe','status']))
                <a href="{{ route('transaksi.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-500 text-[12px] font-semibold px-4 py-2 rounded-lg transition flex items-center">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
            <div>
                <h3 class="text-[12.5px] font-bold text-gray-700">Riwayat Transaksi</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $data->total() }} total data</p>
            </div>
            <span class="flex items-center gap-1.5 text-[10.5px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Live
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/60 border-b border-gray-100">
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Invoice</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Paket</th>
                        <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data as $d)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-4 py-3.5 text-[12px] text-gray-400">
                            {{ $data->firstItem() + $loop->index }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-[11px] font-semibold text-gray-800 group-hover:text-emerald-600 transition">
                                {{ $d->kode_invoice }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="text-[12.5px] font-semibold text-gray-700">{{ $d->created_at->format('d M Y') }}</div>
                            <div class="text-[10.5px] text-gray-400">{{ $d->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="text-[12.5px] font-semibold text-gray-800">
                                {{ $d->member->nama ?? $d->nama_tamu }}
                            </div>
                            <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide
                                    {{ $d->tipe === 'membership' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                                {{ $d->tipe }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-[12.5px] text-gray-600">
                            {{ $d->paket->nama_paket ?? 'Visit Harian' }}
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="text-[13px] font-bold text-gray-900">
                                Rp{{ number_format($d->jumlah_bayar, 0, ',', '.') }}
                            </div>
                            <span class="text-[9.5px] font-semibold uppercase
                                    {{ $d->status === 'dibayar' ? 'text-emerald-500' : ($d->status === 'pending' ? 'text-orange-400' : 'text-red-400') }}">
                                {{ $d->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center">
                            <i class="fa-solid fa-receipt text-3xl text-gray-200 mb-3 block"></i>
                            <p class="text-[12px] text-gray-400">Tidak ada transaksi ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($data->hasPages())
        <div class="px-5 py-3.5 border-t border-gray-100 flex items-center justify-between">
            <p class="text-[11.5px] text-gray-400">
                Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }} dari {{ $data->total() }} transaksi
            </p>
            {{ $data->links() }}
        </div>
        @endif
    </div>
</div>

</div>

@endsection

@push('scripts')
<script>
    const defaultTab = @js($activeTab ?? 'tamu');
    const selectedMemberId = @js($selectedMemberId ?? null);

    function switchTab(tab) {
        ['tamu', 'membership'].forEach(t => {
            document.getElementById('pane-' + t).classList.toggle('hidden', t !== tab);
            document.getElementById('btn-' + t).classList.toggle('active', t === tab);
        });
    }

    const tipe = document.getElementById('tipe_member');
    const formBaru = document.getElementById('form_baru');
    const formPerpanjang = document.getElementById('form_perpanjang');

    tipe.addEventListener('change', function() {
        const isBaru = this.value === 'baru';
        formBaru.classList.toggle('hidden', !isBaru);
        formPerpanjang.classList.toggle('hidden', isBaru);
    });

    document.addEventListener('DOMContentLoaded', () => {
        switchTab(defaultTab);
        if (defaultTab === 'membership' && selectedMemberId) {
            tipe.value = 'perpanjang';
            formBaru.classList.add('hidden');
            formPerpanjang.classList.remove('hidden');
        }
    });
</script>
@endpush