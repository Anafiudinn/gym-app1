<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Verifikasi Pembayaran</h2>
    </x-slot>

    <div class="p-8 max-w-7xl mx-auto space-y-6">

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-green-800 rounded-2xl bg-green-50 border border-green-100 animate-fade-in">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- NAVIGATION TABS --}}
        <div class="flex gap-4 border-b border-gray-100 mb-6">
            <button onclick="switchTab('pending')" id="tab-pending" 
                class="pb-4 px-2 text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 transition-all">
                Perlu Verifikasi 
                <span class="ml-2 bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-[10px]">
                    {{ $data->where('status', 'pending')->count() }}
                </span>
            </button>
            <button onclick="switchTab('history')" id="tab-history" 
                class="pb-4 px-2 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all">
                Riwayat Selesai
            </button>
        </div>

        {{-- 1. SECTION: PERLU VERIFIKASI (PENDING) --}}
        <div id="section-pending" class="space-y-4">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50">
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-4">Member & Paket</th>
                            <th class="px-6 py-4 text-center">Detail Transfer</th>
                            <th class="px-6 py-4 text-center">Bukti</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($data->where('status', 'pending') as $d)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-800">{{ $d->transaksi->member->nama ?? '-' }}</div>
                                <div class="text-[11px] text-indigo-500 font-bold uppercase tracking-tighter">{{ $d->transaksi->paket->nama_paket ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-black text-gray-900">Rp{{ number_format($d->transaksi->jumlah_bayar, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-gray-400 uppercase">{{ $d->nama_bank }} a/n {{ $d->nama_rekening }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ asset('storage/' . $d->bukti_pembayaran) }}" target="_blank" 
                                    class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl hover:bg-indigo-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Cek Foto
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    {{-- FORM TOLAK --}}
                                    <form method="POST" action="/verifikasi/{{ $d->id }}/tolak" class="flex items-center gap-2">
                                        @csrf
                                        <input type="text" name="catatan_admin" placeholder="Alasan tolak..." 
                                            class="text-[10px] border-gray-200 rounded-lg focus:ring-red-500 w-32 py-1">
                                        <button class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white p-2 rounded-xl transition-all" title="Tolak">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                    {{-- FORM TERIMA --}}
                                    <form method="POST" action="/verifikasi/{{ $d->id }}/terima">
                                        @csrf
                                        <button class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-xl shadow-lg shadow-green-100 transition-all flex items-center gap-1 group">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span class="text-xs font-bold pr-1">Terima</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="opacity-20 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="font-bold uppercase tracking-widest text-xs">Semua Pembayaran Sudah Terverifikasi</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 2. SECTION: RIWAYAT SELESAI (SUCCESS/FAILED) --}}
        <div id="section-history" class="hidden space-y-4">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-4">Member</th>
                            <th class="px-6 py-4 text-center">Nominal</th>
                            <th class="px-6 py-4 text-center">Status Akhir</th>
                            <th class="px-6 py-4 text-right">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($data->whereIn('status', ['ditolak', 'diterima']) as $d)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-700">{{ $d->transaksi->member->nama ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400 uppercase font-mono italic">Inv: #{{ $d->transaksi->kode_invoice }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-sm">
                                Rp{{ number_format($d->transaksi->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($d->status == 'diterima')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-700 border border-green-200">Diterima</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-700 border border-red-200">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-xs text-gray-500 italic">
                                {{ $d->catatan_admin ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        function switchTab(type) {
            const tabPending = document.getElementById('tab-pending');
            const tabHistory = document.getElementById('tab-history');
            const secPending = document.getElementById('section-pending');
            const secHistory = document.getElementById('section-history');

            if (type === 'pending') {
                secPending.classList.remove('hidden');
                secHistory.classList.add('hidden');
                tabPending.className = "pb-4 px-2 text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 transition-all";
                tabHistory.className = "pb-4 px-2 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all";
            } else {
                secPending.classList.add('hidden');
                secHistory.classList.remove('hidden');
                tabHistory.className = "pb-4 px-2 text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 transition-all";
                tabPending.className = "pb-4 px-2 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all";
            }
        }
    </script>
</x-app-layout>