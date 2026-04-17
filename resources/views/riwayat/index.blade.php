@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h1>
            <p class="text-sm text-gray-500">Pantau semua arus kas masuk dari Onsite maupun Online</p>
        </div>

        <div class="flex gap-3">
            <div class="bg-white border border-gray-100 p-3 rounded-xl shadow-sm min-w-[120px]">
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Total Record</p>
                <p class="text-lg font-bold text-indigo-600">{{ $data->total() }}</p>
            </div>
        </div>
    </div>
    <div class="flex gap-2 mb-4">
        <a href="{{ route('riwayat.excel', request()->query()) }}" class="bg-emerald-600 text-white px-4 py-2 rounded shadow-sm text-sm font-bold">
            <i class="fa-solid fa-file-excel mr-1"></i> Excel
        </a>
        <a href="{{ route('riwayat.pdf', request()->query()) }}" class="bg-rose-600 text-white px-4 py-2 rounded shadow-sm text-sm font-bold">
            <i class="fa-solid fa-file-pdf mr-1"></i> PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('riwayat.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="text-[11px] font-bold text-gray-400 uppercase mb-1 block">Cari Transaksi</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Invoice, Nama Tamu..."
                    class="w-full bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="text-[11px] font-bold text-gray-400 uppercase mb-1 block">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="text-[11px] font-bold text-gray-400 uppercase mb-1 block">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="text-[11px] font-bold text-gray-400 uppercase mb-1 block">Status Pembayaran</label>
                <select name="status" class="w-full bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-all flex-1">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Filter
                </button>
                <a href="{{ route('riwayat.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-all text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Info Transaksi</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tipe & Paket</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">bukti</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-800 block">#{{ $item->kode_invoice }}</span>
                            <span class="text-[11px] text-gray-400 italic">{{ $item->created_at->format('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->member)
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-700">{{ $item->member->nama }}</span>
                                <span class="text-[11px] px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full w-fit mt-1">Member</span>
                            </div>
                            @else
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-700">{{ $item->nama_tamu }}</span>
                                <span class="text-[11px] px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full w-fit mt-1">Tamu/Harian</span>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700 block">{{ $item->paket->nama_paket ?? '-' }}</span>
                            <span class="text-[11px] text-indigo-500 font-medium capitalize">{{ $item->tipe }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-700 capitalize">{{ $item->metode_pembayaran }}</span>
                                <span class="text-[11px] font-medium {{ $item->channel == 'onsite' ? 'text-orange-500' : 'text-purple-500' }} uppercase tracking-tighter">
                                    ● {{ $item->channel }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                            $statusClasses = [
                            'dibayar' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                            'menunggu' => 'bg-amber-50 text-amber-600 border-amber-100',
                            'ditolak' => 'bg-rose-50 text-rose-600 border-rose-100',
                            ];
                            $class = $statusClasses[$item->status] ?? 'bg-gray-50 text-gray-600';
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $class }} uppercase">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{-- Kita panggil lewat relasi 'verifikasi' --}}
                            @if($item->verifikasi && $item->verifikasi->bukti_pembayaran)
                            <a href="{{ asset('storage/' . $item->verifikasi->bukti_pembayaran) }}"
                                target="_blank"
                                class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-bold decoration-2 underline-offset-4 hover:underline">
                                <i class="fa-solid fa-image mr-1.5"></i> Lihat Bukti
                            </a>
                            @else
                            <span class="text-[11px] text-gray-400 italic bg-gray-50 px-2 py-1 rounded">
                                Tidak ada bukti
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-800">
                            Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">
                            Belum ada riwayat transaksi ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-50 bg-gray-50/30">
            {{ $data->links() }}
        </div>
    </div>
</div>
@endsection