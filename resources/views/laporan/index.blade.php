<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Laporan Keuangan</h2>
            {{-- Tombol Cetak (Opsional: bisa diarahkan ke fungsi window.print() atau PDF) --}}
            <button onclick="window.print()" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Laporan
            </button>
        </div>
    </x-slot>

    <div class="p-4 max-w-7xl mx-auto space-y-6">

        {{-- ========================= --}}
        {{-- 🔹 FILTER AREA --}}
        {{-- ========================= --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <form method="GET" action="/laporan" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase ml-1">Cari Nama/Invoice</label>
                    <input type="text" name="search" placeholder="Contoh: Budi..."
                        value="{{ request('search') }}"
                        class="w-full border-gray-200 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase ml-1">Dari Tanggal</label>
                    <input type="date" name="tanggal_awal"
                        value="{{ request('tanggal_awal') }}"
                        class="w-full border-gray-200 rounded-2xl focus:ring-indigo-500 text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase ml-1">Sampai Tanggal</label>
                    <input type="date" name="tanggal_akhir"
                        value="{{ request('tanggal_akhir') }}"
                        class="w-full border-gray-200 rounded-2xl focus:ring-indigo-500 text-sm">
                </div>

                <div class="flex gap-2">
                    <button class="flex-1 bg-indigo-600 text-white font-bold py-3 rounded-2xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                        Tampilkan
                    </button>
                    <a href="/laporan" class="bg-gray-100 text-gray-500 p-3 rounded-2xl hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </a>
                </div>
            </form>
        </div>

        {{-- ========================= --}}
        {{-- 🔹 SUMMARY CARDS --}}
        {{-- ========================= --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-indigo-600 p-6 rounded-3xl shadow-xl shadow-indigo-100 relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-indigo-100 text-xs font-bold uppercase tracking-widest">Total Pemasukan</p>
                    <h3 class="text-3xl font-black text-white mt-1">Rp{{ number_format($total, 0, ',', '.') }}</h3>
                </div>
                {{-- Dekorasi Ikon Transparan --}}
                <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-indigo-500 opacity-50" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Transaksi</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $data->count() }} <span class="text-sm font-medium text-gray-400">Record</span></h3>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Periode Laporan</p>
                <h3 class="text-lg font-bold text-gray-800 mt-2">
                    @if(request('tanggal_awal'))
                        {{ date('d/m/y', strtotime(request('tanggal_awal'))) }} - {{ date('d/m/y', strtotime(request('tanggal_akhir'))) }}
                    @else
                        Semua Waktu
                    @endif
                </h3>
            </div>
        </div>

        {{-- ========================= --}}
        {{-- 🔹 DATA TABLE --}}
        {{-- ========================= --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                            <th class="px-6 py-5">Tanggal</th>
                            <th class="px-6 py-5">Pelanggan</th>
                            <th class="px-6 py-5 text-center">Tipe</th>
                            <th class="px-6 py-5 text-center">Paket</th>
                            <th class="px-6 py-5 text-right">Jumlah</th>
                            <th class="px-6 py-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($data as $d)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-700">{{ $d->tanggal_pembayaran ? date('d M Y', strtotime($d->tanggal_pembayaran)) : '-' }}</div>
                                <div class="text-[10px] text-gray-400 font-mono italic">ID: {{ $d->kode_invoice ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-800">{{ $d->member->nama ?? $d->nama_tamu }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[10px] font-black uppercase px-2 py-1 rounded-lg bg-gray-100 text-gray-500">
                                    {{ $d->tipe }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600">{{ $d->paket->nama_paket ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-black text-gray-900 leading-none">Rp{{ number_format($d->jumlah_bayar, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($d->status == 'dibayar' || $d->status == 'Lunas')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-green-600">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                        Lunas
                                    </span>
                                @else
                                    <span class="text-[10px] font-black uppercase text-gray-400">{{ $d->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-gray-400 italic">
                                Tidak ada data yang ditemukan untuk filter ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    {{-- Footer Tabel untuk Total --}}
                    <tfoot class="bg-gray-50/50 border-t-2 border-gray-100">
                        <tr>
                            <td colspan="4" class="px-6 py-5 text-sm font-black text-gray-800 text-right uppercase tracking-widest">Grand Total</td>
                            <td class="px-6 py-5 text-right">
                                <span class="text-lg font-black text-indigo-600 leading-none">Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    <style>
        @media print {
            nav, button, form, .bg-indigo-600 svg {
                display: none !important;
            }
            .shadow-xl, .shadow-sm {
                shadow: none !important;
                border: 1px solid #eee !important;
            }
            .bg-indigo-600 {
                background-color: #fff !important;
                color: #000 !important;
                border: 2px solid #000 !important;
            }
            .text-indigo-100, .text-white {
                color: #000 !important;
            }
        }
    </style>
</x-app-layout>