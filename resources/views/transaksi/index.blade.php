<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Kasir Transaksi</h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        
        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="mb-6 flex items-center p-4 text-green-800 rounded-2xl bg-green-50 border border-green-100 animate-fade-in">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- KIRI: INPUT AREA --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    {{-- TAB NAVIGATION --}}
                    <div class="flex border-b border-gray-50">
                        <button onclick="switchTab('tamu')" id="btn-tamu" class="flex-1 py-4 text-sm font-bold transition-all border-b-2 border-indigo-600 text-indigo-600 bg-indigo-50/30">
                            Tamu Harian
                        </button>
                        <button onclick="switchTab('membership')" id="btn-membership" class="flex-1 py-4 text-sm font-bold transition-all border-b-2 border-transparent text-gray-400 hover:text-gray-600">
                            Membership
                        </button>
                    </div>

                    <div class="p-8">
                        {{-- FORM TAMU HARIAN --}}
                        <div id="pane-tamu" class="space-y-4">
                            <div class="bg-blue-50 p-4 rounded-2xl mb-6 flex justify-between items-center">
                                <div>
                                    <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">Paket Default</p>
                                    <h4 class="text-lg font-black text-blue-900">Visit Harian</h4>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-blue-600 italic">Rp20k</span>
                                </div>
                            </div>
                            
                            <form method="POST" action="/transaksi/harian">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-xs font-bold text-gray-400 uppercase ml-1">Nama Pengunjung</label>
                                        <input type="text" name="nama_tamu" placeholder="Input nama tamu..." 
                                            class="w-full mt-1 border-gray-200 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 p-4" required>
                                    </div>
                                    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all transform active:scale-95">
                                        BAYAR SEKARANG
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- FORM MEMBERSHIP --}}
                        <div id="pane-membership" class="hidden space-y-4">
                            <form method="POST" action="/transaksi/membership">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-xs font-bold text-gray-400 uppercase ml-1">Tipe Transaksi</label>
                                        <select name="tipe_member" id="tipe_member" class="w-full mt-1 border-gray-200 rounded-2xl focus:ring-indigo-500" required>
                                            <option value="baru">✨ Member Baru</option>
                                            <option value="perpanjang">🔄 Perpanjang Member</option>
                                        </select>
                                    </div>

                                    <div id="form_baru" class="space-y-4 p-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                        <input type="text" name="nama" placeholder="Nama Lengkap" class="w-full border-gray-200 rounded-xl">
                                        <input type="text" name="no_wa" placeholder="No. WhatsApp (628...)" class="w-full border-gray-200 rounded-xl">
                                        <select name="jenis_kelamin" class="w-full border-gray-200 rounded-xl">
                                            <option value="">Pilih Gender</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>

                                    <div id="form_perpanjang" class="hidden">
                                        <label class="text-xs font-bold text-gray-400 uppercase ml-1">Pilih Member</label>
                                        <select name="member_id" class="w-full mt-1 border-gray-200 rounded-2xl select2">
                                            <option value="">-- Cari Nama / Kode --</option>
                                            @foreach($members as $m)
                                                <option value="{{ $m->id }}">{{ $m->nama }} ({{ $m->kode_member }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-gray-400 uppercase ml-1">Pilih Paket</label>
                                        <select name="paket_id" class="w-full mt-1 border-gray-200 rounded-2xl font-bold text-indigo-600" required>
                                            <option value="">-- Pilih Durasi Paket --</option>
                                            @foreach($paket as $p)
                                                <option value="{{ $p->id }}">{{ $p->nama_paket }} - Rp{{ number_format($p->harga, 0, ',', '.') }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-green-100 transition-all transform active:scale-95">
                                        PROSES MEMBERSHIP
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: RECENT TRANSACTIONS --}}
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-black text-gray-800 uppercase tracking-tighter">Riwayat Transaksi Terakhir</h3>
                        <span class="text-xs font-bold bg-white px-3 py-1 rounded-full border shadow-sm text-gray-500">Live Updates</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                                    <th class="px-6 py-4">Invoice</th>
                                    <th class="px-6 py-4">Pelanggan</th>
                                    <th class="px-6 py-4">Item/Paket</th>
                                    <th class="px-6 py-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($data as $d)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-mono font-bold text-gray-400 group-hover:text-indigo-600 italic">#{{ $d->kode_invoice }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-800">{{ $d->member->nama ?? $d->nama_tamu }}</div>
                                        <div class="text-[10px] text-gray-400 uppercase font-black">{{ $d->tipe }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $d->paket->nama_paket ?? 'Visit Harian' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="text-sm font-black text-gray-900 leading-none">Rp{{ number_format($d->jumlah_bayar, 0, ',', '.') }}</div>
                                        <span class="text-[9px] font-bold text-green-500 uppercase">{{ $d->status}}</span>
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

    {{-- SCRIPTS --}}
    <script>
        // Switch between Harian and Membership Tabs
        function switchTab(tab) {
            const paneTamu = document.getElementById('pane-tamu');
            const paneMember = document.getElementById('pane-membership');
            const btnTamu = document.getElementById('btn-tamu');
            const btnMember = document.getElementById('btn-membership');

            if(tab === 'tamu') {
                paneTamu.classList.remove('hidden');
                paneMember.classList.add('hidden');
                btnTamu.className = "flex-1 py-4 text-sm font-bold transition-all border-b-2 border-indigo-600 text-indigo-600 bg-indigo-50/30";
                btnMember.className = "flex-1 py-4 text-sm font-bold transition-all border-b-2 border-transparent text-gray-400 hover:text-gray-600";
            } else {
                paneTamu.classList.add('hidden');
                paneMember.classList.remove('hidden');
                btnMember.className = "flex-1 py-4 text-sm font-bold transition-all border-b-2 border-indigo-600 text-indigo-600 bg-indigo-50/30";
                btnTamu.className = "flex-1 py-4 text-sm font-bold transition-all border-b-2 border-transparent text-gray-400 hover:text-gray-600";
            }
        }

        // Handle Member Baru vs Perpanjang inside Membership Tab
        const tipe = document.getElementById('tipe_member');
        const formBaru = document.getElementById('form_baru');
        const formPerpanjang = document.getElementById('form_perpanjang');

        tipe.addEventListener('change', function () {
            if (this.value === 'baru') {
                formBaru.classList.remove('hidden');
                formPerpanjang.classList.add('hidden');
            } else {
                formBaru.classList.add('hidden');
                formPerpanjang.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>