@extends('layouts.admin')
@section('title', 'Presensi User')
@section('page-title', 'Presensi User')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- KIRI: SCANNER & PREVIEW --}}
  {{-- KIRI: SCANNER & PREVIEW --}}
<div class="lg:col-span-4 space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="mb-5">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Quick Absen</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">Kode member · nama · no WA</p>
        </div>

        <div class="space-y-3">
            <input type="text" id="input_kode" autofocus
                class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl text-center text-base font-mono font-bold text-emerald-600 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none transition uppercase placeholder:text-gray-300 placeholder:font-normal placeholder:not-italic"
                placeholder="Cari kode / nama / no WA">

            <button type="button" onclick="cekMember()" id="btn-cek"
                class="w-full bg-gray-900 hover:bg-black text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Cek Status
            </button>
        </div>

        {{-- SISIPKAN KODE INI DI SINI --}}
        <div id="list-multiple" class="hidden mt-4 p-4 border rounded-xl bg-white shadow-sm border-dashed border-gray-300">
            <h3 class="mb-3 font-bold text-gray-700 text-sm">Pilih Member:</h3>
            <div id="container-pilihan"></div>
        </div>
        {{-- AKHIR SISIPAN --}}

        {{-- PREVIEW CARD --}}
        <div id="preview-member" class="hidden mt-5 pt-5 border-t border-dashed border-gray-200">
            <div id="status-container" class="rounded-xl p-4 border">
                {{-- ... (Isi preview member tetap sama) ... --}}
                
                <div class="flex items-center gap-3 mb-4">
                    <div id="p-avatar" class="w-11 h-11 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm">
                        <span id="p-initial">--</span>
                    </div>
                    <div>
                        <h4 id="p-nama" class="font-bold text-gray-800 text-sm leading-tight">--</h4>
                        <p id="p-kode" class="text-[10px] font-mono text-emerald-600 mt-0.5">--</p>
                    </div>
                </div>

                    <div class="space-y-1.5 mb-4 pt-3 border-t border-dashed border-gray-200/70">
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Status Akun</span>
                            <span id="p-status" class="font-bold uppercase tracking-wide">--</span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Masa Berlaku</span>
                            <span id="p-expired" class="font-semibold text-gray-700">--</span>
                        </div>
                    </div>

                    {{-- Action area --}}
                    <form id="form-absen" method="POST" action="{{ route('absensi.store') }}" class="hidden">
                        @csrf
                        <input type="hidden" name="member_id" id="p-id">
                        <button type="submit"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 rounded-xl transition text-sm shadow-md shadow-emerald-100">
                            <i class="fa-solid fa-check mr-1.5 text-xs"></i> Konfirmasi Masuk
                        </button>
                    </form>

                    <a id="btn-manage" href="{{ route('member.index') }}"
                        class="hidden w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition text-sm shadow-md shadow-orange-100 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-gear text-xs"></i>
                        Perpanjang / Edit Member
                    </a>
                </div>

                {{-- Pesan double absen --}}
                <div id="msg-double" class="hidden mt-3 text-center text-[11px] text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    <span id="msg-double-text"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- KANAN: LOG ABSENSI --}}
    <div class="lg:col-span-8 space-y-4">

        {{-- Filter --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('absensi.index') }}" method="GET" class="flex flex-wrap gap-2.5">
                <div class="flex-1 min-w-[180px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, kode, atau no WA..."
                        class="w-full bg-gray-50 border border-gray-200 text-xs rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none">
                </div>
                <div>
                    <input type="date" name="date" value="{{ $date }}"
                        class="w-full bg-gray-50 border border-gray-200 text-xs rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 outline-none">
                </div>
                <button type="submit" class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-emerald-600 transition">
                    Filter
                </button>
                @if(request('search') || request('date'))
                <a href="{{ route('absensi.index') }}"
                    class="bg-gray-100 text-gray-500 px-4 py-2 rounded-lg text-xs font-semibold hover:bg-gray-200 transition">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Log Absensi</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }} &middot;
                        <span class="font-semibold text-emerald-600">{{ $totalHariIni }} orang masuk</span>
                    </p>
                </div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg">
                    <i class="fa-solid fa-circle text-[5px] animate-pulse"></i> LIVE
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/60">
                        <tr>
                            <th class="px-6 py-3.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Member</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Waktu Masuk</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($absensi as $row)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center font-bold text-emerald-600 text-[10px] group-hover:bg-emerald-100 transition">
                                        {{ strtoupper(substr($row->member->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-[13px] font-semibold text-gray-800">{{ $row->member->nama }}</div>
                                        <div class="text-[10px] font-mono text-gray-400">{{ $row->member->kode_member }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="text-[13px] font-bold text-gray-700">{{ $row->created_at->format('H:i') }}</span>
                                <span class="text-[10px] text-gray-400 block">{{ $row->created_at->format('d/m/y') }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[9px] font-black uppercase bg-emerald-100 text-emerald-700">
                                    <i class="fa-solid fa-check text-[8px]"></i> Berhasil
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <i class="fa-solid fa-calendar-xmark text-3xl text-gray-200 mb-3 block"></i>
                                <p class="text-[12px] text-gray-400">Belum ada absensi untuk tanggal ini</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($absensi->hasPages())
            <div class="px-6 py-3.5 border-t border-gray-50">
                {{ $absensi->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function cekMember() {
        const kode = document.getElementById('input_kode').value.trim();
        const btn = document.getElementById('btn-cek');
        const listContainer = document.getElementById('list-multiple');
        const containerPilihan = document.getElementById('container-pilihan');
        const preview = document.getElementById('preview-member');

        if (!kode) return;

        // Reset UI
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-sm"></i>';
        listContainer.classList.add('hidden');
        preview.classList.add('hidden');

        fetch(`{{ route('absensi.cek') }}?keyword=${encodeURIComponent(kode)}`, {
            headers: {
                'Accept': 'application/json',
                'ngrok-skip-browser-warning': 'true'
            }
        })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(res => {
            // 1. Handle JIKA HASIL GANDA (Multiple)
            if (res.multiple) {
                listContainer.classList.remove('hidden');
                containerPilihan.innerHTML = ''; 
                
                res.data.forEach(m => {
                    const btn = document.createElement('button');
                    btn.className = "w-full text-left p-3 border rounded-lg mb-2 hover:bg-gray-50 flex justify-between items-center transition";
                    btn.innerHTML = `<div><div class="font-bold">${m.nama}</div><div class="text-xs text-gray-500">${m.kode_member}</div></div>`;
                    
                    btn.onclick = () => {
                        document.getElementById('input_kode').value = m.kode_member;
                        cekMember(); // Trigger ulang
                    };
                    containerPilihan.appendChild(btn);
                });
                return;
            }

            // 2. Handle GAGAL / TIDAK DITEMUKAN
            if (!res.success) {
                showToast(res.message, 'error');
                return;
            }

            // 3. Handle SUKSES
            const d = res.data;
            const formAbsen = document.getElementById('form-absen');
            const btnManage = document.getElementById('btn-manage');
            const msgDouble = document.getElementById('msg-double');
            const container = document.getElementById('status-container');
            const avatar = document.getElementById('p-avatar');
            const statusEl = document.getElementById('p-status');

            msgDouble.classList.add('hidden');
            formAbsen.classList.add('hidden');
            btnManage.classList.add('hidden');

            document.getElementById('p-nama').innerText = d.nama;
            document.getElementById('p-kode').innerText = d.kode;
            document.getElementById('p-initial').innerText = d.nama.substring(0, 2).toUpperCase();
            document.getElementById('p-id').value = d.id;
            document.getElementById('p-expired').innerText = d.expired_at;

            if (!d.can_absen) {
                const label = d.is_expired ? 'Membership Expired' : 'Non-Aktif';
                statusEl.innerText = label;
                statusEl.className = 'font-bold uppercase tracking-wide text-red-600';
                container.className = 'rounded-xl p-4 border bg-red-50 border-red-100';
                avatar.className = 'w-11 h-11 rounded-full flex items-center justify-center text-white font-bold text-sm bg-red-400';
                btnManage.classList.remove('hidden');
                btnManage.href = d.manage_url;
            } else {
                statusEl.innerText = 'Aktif';
                statusEl.className = 'font-bold uppercase tracking-wide text-emerald-600';
                container.className = 'rounded-xl p-4 border bg-emerald-50/60 border-emerald-100';
                avatar.className = 'w-11 h-11 rounded-full flex items-center justify-center text-white font-bold text-sm bg-emerald-500';

                if (d.sudah_absen) {
                    msgDouble.classList.remove('hidden');
                    document.getElementById('msg-double-text').innerText = d.nama + ' sudah absen hari ini';
                } else {
                    formAbsen.classList.remove('hidden');
                }
            }
            preview.classList.remove('hidden');
            preview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        })
        .catch(err => {
            showToast('Gagal: ' + err.message, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-magnifying-glass text-xs"></i> Cek Status';
        });
    }

    function showToast(msg, type = 'info') {
        const existing = document.getElementById('toast-notif');
        if (existing) existing.remove();
        const colors = type === 'error' ? 'bg-red-600 text-white' : 'bg-emerald-600 text-white';
        const toast = document.createElement('div');
        toast.id = 'toast-notif';
        toast.className = `fixed top-5 left-1/2 -translate-x-1/2 z-50 px-5 py-3 rounded-xl shadow-lg text-sm font-semibold ${colors} transition-all`;
        toast.innerText = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    document.getElementById('input_kode').addEventListener('keypress', e => {
        if (e.key === 'Enter') cekMember();
    });
</script>
@endpush