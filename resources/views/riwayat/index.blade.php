@extends('layouts.admin')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')

@section('content')
<div class="space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-800 leading-tight">Riwayat Transaksi</h1>
            <p class="text-xs text-gray-400 mt-0.5">Pantau semua arus kas masuk dari Onsite maupun Online</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('riwayat.excel', request()->query()) }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-colors shadow-sm">
                <i class="fa-solid fa-file-excel text-[11px]"></i> Export Excel
            </a>
            <a href="{{ route('riwayat.pdf', request()->query()) }}"
               class="inline-flex items-center gap-1.5 bg-rose-500 hover:bg-rose-600 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-colors shadow-sm">
                <i class="fa-solid fa-file-pdf text-[11px]"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Total Pendapatan --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-wallet text-emerald-500 text-base"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Pendapatan Terfilter</p>
                <p class="text-xl font-black text-gray-800 truncate">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">*Hanya transaksi berstatus <span class="font-semibold text-emerald-600">Dibayar</span></p>
            </div>
        </div>

        {{-- Total Record --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-receipt text-indigo-500 text-base"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Record</p>
                <p class="text-xl font-black text-gray-800">{{ $data->total() }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Transaksi ditemukan</p>
            </div>
        </div>

    </div>

    {{-- ===== FILTER PANEL ===== --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-3">Filter & Pencarian</p>
        <form action="{{ route('riwayat.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">

                {{-- Search --}}
                <div class="xl:col-span-2">
                    <label class="text-[10px] font-semibold text-gray-400 uppercase block mb-1">Cari Transaksi</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-[11px]"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Invoice, nama tamu..."
                               class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-100 rounded-lg text-xs text-gray-700 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                </div>

                {{-- Dari Tanggal --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-400 uppercase block mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                {{-- Sampai Tanggal --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-400 uppercase block mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                {{-- Channel --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-400 uppercase block mb-1">Channel</label>
                    <select name="channel"
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="">Semua</option>
                        <option value="onsite"  {{ request('channel') == 'onsite'  ? 'selected' : '' }}>Onsite</option>
                        <option value="online"  {{ request('channel') == 'online'  ? 'selected' : '' }}>Online</option>
                    </select>
                </div>

                {{-- Tipe Paket --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-400 uppercase block mb-1">Tipe Paket</label>
                    <select name="tipe"
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="">Semua</option>
                        <option value="membership" {{ request('tipe') == 'membership' ? 'selected' : '' }}>Membership</option>
                        <option value="harian"     {{ request('tipe') == 'harian'     ? 'selected' : '' }}>Harian</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-400 uppercase block mb-1">Status</label>
                    <select name="status"
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="">Semua</option>
                        <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="flex items-end gap-2 xl:col-span-2">
                    <button type="submit"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Filter
                    </button>
                    <a href="{{ route('riwayat.index') }}"
                       class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                    </a>
                </div>

            </div>
        </form>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-gray-400">Info Transaksi</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-gray-400">Pelanggan</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-gray-400">Tipe & Paket</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-gray-400">Pembayaran</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-gray-400">Bukti</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data as $item)
                    <tr class="hover:bg-gray-50/40 transition-colors group">

                        {{-- Info Transaksi --}}
                        <td class="px-5 py-4">
                            <span class="text-xs font-bold text-gray-800 block">#{{ $item->kode_invoice }}</span>
                            <span class="text-[10px] text-gray-400">{{ $item->created_at->format('d M Y, H:i') }}</span>
                        </td>

                        {{-- Pelanggan --}}
                        <td class="px-5 py-4">
                            @if($item->member)
                                <span class="text-xs font-semibold text-gray-700 block">{{ $item->member->nama }}</span>
                                <span class="inline-block mt-1 text-[10px] px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full font-medium">Member</span>
                            @else
                                <span class="text-xs font-semibold text-gray-700 block">{{ $item->nama_tamu }}</span>
                                <span class="inline-block mt-1 text-[10px] px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full font-medium">Tamu / Harian</span>
                            @endif
                        </td>

                        {{-- Tipe & Paket --}}
                        <td class="px-5 py-4">
                            <span class="text-xs text-gray-700 block">{{ $item->paket->nama_paket ?? '-' }}</span>
                            <span class="text-[10px] text-indigo-500 font-semibold capitalize">{{ $item->tipe }}</span>
                        </td>

                        {{-- Pembayaran --}}
                        <td class="px-5 py-4">
                            <span class="text-xs text-gray-700 capitalize block">{{ $item->metode_pembayaran }}</span>
                            <span class="text-[10px] font-semibold uppercase tracking-tight
                                {{ $item->channel == 'onsite' ? 'text-orange-500' : 'text-purple-500' }}">
                                ● {{ $item->channel }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4">
                            @php
                                $statusMap = [
                                    'dibayar'  => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'menunggu' => 'bg-amber-50   text-amber-600   border-amber-100',
                                    'pending'  => 'bg-amber-50   text-amber-600   border-amber-100',
                                    'ditolak'  => 'bg-rose-50    text-rose-600    border-rose-100',
                                ];
                                $cls = $statusMap[$item->status] ?? 'bg-gray-50 text-gray-500 border-gray-100';
                            @endphp
                            <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold border uppercase {{ $cls }}">
                                {{ $item->status }}
                            </span>
                        </td>

                        {{-- Bukti --}}
                        <td class="px-5 py-4">
                            @if($item->verifikasi && $item->verifikasi->bukti_pembayaran)
                                <a href="{{ asset('storage/' . $item->verifikasi->bukti_pembayaran) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:underline underline-offset-2 transition">
                                    <i class="fa-solid fa-image text-[10px]"></i> Lihat Bukti
                                </a>
                            @else
                                <span class="text-[10px] text-gray-400 italic bg-gray-50 border border-gray-100 px-2 py-1 rounded-lg">
                                    Tidak ada
                                </span>
                            @endif
                        </td>

                        {{-- Nominal --}}
                        <td class="px-5 py-4 text-right">
                            <span class="text-xs font-bold text-gray-800">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</span>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <i class="fa-solid fa-clock-rotate-left text-3xl"></i>
                                <p class="text-sm font-medium text-gray-400">Tidak ada riwayat transaksi ditemukan.</p>
                                <a href="{{ route('riwayat.index') }}" class="text-xs text-indigo-500 hover:underline mt-1">Reset filter</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-3.5 border-t border-gray-50 bg-gray-50/30 flex items-center justify-between gap-3 flex-wrap">
            <p class="text-[11px] text-gray-400">
                Menampilkan <span class="font-semibold text-gray-600">{{ $data->firstItem() ?? 0 }}</span>–<span class="font-semibold text-gray-600">{{ $data->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-gray-600">{{ $data->total() }}</span> record
            </p>
            <div class="text-xs">
                {{ $data->links() }}
            </div>
        </div>
    </div>

</div>
@endsection