<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Presensi Member</h2>
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500 bg-gray-100 px-4 py-2 rounded-lg">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                Sistem Live: {{ date('d M Y') }}
            </div>
        </div>
    </x-slot>

    <div class="p-8 max-w-[1600px] mx-auto">
        
        {{-- NOTIFIKASI DENGAN ANIMASI --}}
        @if(session('success'))
            <div class="flex items-center p-4 mb-6 text-green-800 rounded-2xl bg-green-50 border border-green-100 animate-bounce-short">
                <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center p-4 mb-6 text-red-800 rounded-2xl bg-red-50 border border-red-100">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-bold">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- KIRI: KIOSK SCANNER --}}
            <div class="lg:col-span-4">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 sticky top-8">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-800">Scan Member</h3>
                        <p class="text-sm text-gray-400 mt-1">Arahkan barcode ke scanner atau ketik manual</p>
                    </div>

                    <form method="POST" action="/absensi" class="space-y-4">
                        @csrf
                        <div class="relative">
                            <input type="text" name="kode_member" 
                                placeholder="Input Kode..." 
                                class="w-full bg-gray-50 border-2 border-gray-100 p-5 rounded-2xl text-center text-2xl font-mono font-bold text-indigo-600 focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all uppercase placeholder:text-gray-300" 
                                autofocus required>
                        </div>
                        
                        <button class="w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-2xl shadow-lg shadow-gray-200 transition-all flex items-center justify-center gap-2 group">
                            <span>Konfirmasi Masuk</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                    </form>

                    <div class="mt-8 pt-8 border-t border-gray-50">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400 font-medium">Jam Digital</span>
                            <span id="liveClock" class="font-mono font-bold text-indigo-600">00:00:00</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: LOG AKTIVITAS --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
                    
                    {{-- HEADER & FILTER --}}
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                        <form method="GET" action="/absensi" class="flex flex-col md:flex-row gap-4">
                            <div class="relative flex-1">
                                <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    placeholder="Cari Member..." 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border-gray-200 rounded-xl focus:ring-indigo-500 text-sm">
                            </div>
                            
                            <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                                class="border-gray-200 rounded-xl text-sm focus:ring-indigo-500">

                            <div class="flex gap-2">
                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-100">
                                    Filter
                                </button>
                                <a href="/absensi" class="bg-white border border-gray-200 text-gray-500 px-4 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition-colors flex items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- TABEL --}}
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Informasi Member</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Waktu Presensi</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Status Keanggotaan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($absensi as $row)
                                <tr class="hover:bg-indigo-50/20 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center font-bold text-indigo-600 text-xs">
                                                {{ substr($row->member->nama, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900 leading-none mb-1">{{ $row->member->nama }}</div>
                                                <div class="text-xs font-mono text-indigo-500 bg-indigo-50 px-1.5 py-0.5 rounded inline-block">{{ $row->member->kode_member }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="text-sm font-bold text-gray-800">{{ $row->waktu_masuk->format('H:i:s') }}</div>
                                        <div class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter italic">{{ $row->waktu_masuk->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($row->status == 'valid')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-100 text-green-700 border border-green-200">
                                                Valid
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-700 border border-red-200">
                                                Expired
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center opacity-20">
                                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="font-bold">Belum Ada Aktivitas Presensi</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    @if($absensi->hasPages())
                    <div class="p-6 border-t border-gray-50 bg-gray-50/20">
                        {{ $absensi->links() }}
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <script>
        // Script Sederhana untuk Jam Digital
        setInterval(() => {
            const now = new Date();
            document.getElementById('liveClock').innerText = now.toLocaleTimeString('id-ID');
        }, 1000);
    </script>

    <style>
        @keyframes bounce-short {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        .animate-bounce-short {
            animation: bounce-short 1s ease-in-out infinite;
        }
    </style>
</x-app-layout>