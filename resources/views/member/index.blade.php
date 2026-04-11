<x-app-layout>
   <x-slot name="header">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <h2 class="text-2xl font-bold text-gray-800 leading-tight">
            {{ __('Manajemen Member') }}
        </h2>
        
        <form action="/member" method="GET" class="w-full md:w-72">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari nama atau kode..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </form>
    </div>
</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Kode</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Member</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">No. WhatsApp</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Expired</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($members as $m)
                            <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                                        {{ $m->kode_member }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $m->nama }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $m->no_wa }}
                                </td>
                                {{-- Kolom Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($m->status == 'aktif')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-2 h-2 mr-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </span>
                                    @elseif($m->status == 'expired')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                        <span class="w-2 h-2 mr-1.5 rounded-full bg-orange-500"></span>
                                        Habis Masa Aktif
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-2 h-2 mr-1.5 rounded-full bg-red-500"></span>
                                        Nonaktif (Banned)
                                    </span>
                                    @endif
                                </td>

                                {{-- Kolom Expired --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($m->tanggal_kadaluarsa)
                                    <div class="text-gray-900 font-medium">
                                        {{ \Carbon\Carbon::parse($m->tanggal_kadaluarsa)->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($m->tanggal_kadaluarsa)->diffForHumans() }}
                                    </div>
                                    @else
                                    <span class="text-gray-400 italic">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center space-x-2">
                                        <a href="/member/{{ $m->id }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Detail
                                        </a>

                                        <form action="/member/{{ $m->id }}/toggle" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 {{ $m->status == 'aktif' ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }} border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition duration-150">
                                                {{ $m->status == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <p>Belum ada data member.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($members, 'links'))
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $members->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>