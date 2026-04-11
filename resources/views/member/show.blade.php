<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="/member" class="text-gray-400 hover:text-indigo-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">Detail Member</h2>
        </div>
    </x-slot>

    <div class="p-8 max-w-7xl mx-auto space-y-8">

        {{-- BAGIAN ATAS: PROFIL SINGKAT --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-24"></div>
            <div class="px-8 pb-8">
                <div class="relative flex justify-between items-end -mt-12 mb-6">
                    <div class="p-1 bg-white rounded-2xl shadow-sm">
                        <div class="w-24 h-24 bg-gray-100 rounded-xl flex items-center justify-center text-indigo-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $member->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $member->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Nama Lengkap</p>
                        <p class="text-lg font-bold text-gray-800">{{ $member->nama }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">ID Member</p>
                        <p class="text-lg font-mono font-bold text-indigo-600">{{ $member->kode_member }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">WhatsApp</p>
                        <p class="text-lg font-medium text-gray-700">{{ $member->no_wa }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Masa Berlaku</p>
                        <p class="text-lg font-medium text-gray-700">{{ $member->tanggal_kadaluarsa ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- RIWAYAT TRANSAKSI --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                        Riwayat Transaksi
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase">Invoice</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase">Paket</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($transaksi as $t)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $t->kode_invoice }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $t->paket->nama_paket ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">Rp{{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- RIWAYAT MEMBERSHIP --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-purple-500 rounded-full"></span>
                        Riwayat Membership
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase">Paket</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase">Periode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($membership as $m)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $m->paket->nama_paket }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <span class="block">{{ \Carbon\Carbon::parse($m->tanggal_mulai)->format('d M Y') }}</span>
                                    <span class="text-xs text-gray-400">s/d {{ \Carbon\Carbon::parse($m->tanggal_selesai)->format('d M Y') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>