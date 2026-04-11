<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Manajemen Paket</h2>
    </x-slot>

    <div class="p-8 max-w-7xl mx-auto space-y-10">

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-green-800 rounded-xl bg-green-50 border border-green-100 animate-fade-in-down">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- 🔹 FORM TAMBAH PAKET (Sticky) --}}
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg">Tambah Paket</h3>
                    </div>

                    <form method="POST" action="/paket" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase ml-1">Nama Paket</label>
                            <input type="text" name="nama_paket" placeholder="Contoh: Premium Monthly"
                                class="w-full mt-1 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase ml-1">Harga (Rp)</label>
                                <input type="number" name="harga" placeholder="0"
                                    class="w-full mt-1 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase ml-1">Durasi (Hari)</label>
                                <input type="number" name="durasi_hari" placeholder="30"
                                    class="w-full mt-1 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase ml-1">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" placeholder="Opsional..."
                                class="w-full mt-1 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all"></textarea>
                        </div>

                        <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-md shadow-indigo-100 transition-all transform active:scale-[0.98]">
                            Simpan Paket
                        </button>
                    </form>
                </div>
            </div>

            {{-- 🔹 TABEL DATA PAKET --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 text-lg">Daftar Paket Aktif</h3>
                        <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">{{ count($paket) }} Paket</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Detail Paket</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Harga & Durasi</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($paket as $p)
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <form method="POST" action="/paket/{{ $p->id }}">
                                        @csrf
                                        @method('PUT')
                                        
                                        <td class="px-6 py-4">
                                            <input type="text" name="nama_paket" value="{{ $p->nama_paket }}" 
                                                class="font-bold text-gray-800 border-transparent bg-transparent focus:border-indigo-500 focus:bg-white rounded-lg p-1 w-full transition-all">
                                            <span class="text-xs text-gray-400 block mt-1 ml-1">ID: #{{ $p->id }}</span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center text-sm font-bold text-gray-900 leading-none">
                                                    <span class="text-gray-400 mr-1 text-xs uppercase">Rp</span>
                                                    <input type="number" name="harga" value="{{ $p->harga }}" 
                                                        class="border-transparent bg-transparent focus:border-indigo-500 focus:bg-white rounded-lg p-0 w-24 tracking-tight">
                                                </div>
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <input type="number" name="durasi_hari" value="{{ $p->durasi_hari }}" 
                                                        class="border-transparent bg-transparent focus:border-indigo-500 focus:bg-white rounded-lg p-0 w-8 text-xs"> Hari
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex justify-center items-center gap-2">
                                                <button type="submit" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Update">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                </button>
                                    </form>

                                                <form method="POST" action="/paket/{{ $p->id }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Hapus paket ini?')" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>