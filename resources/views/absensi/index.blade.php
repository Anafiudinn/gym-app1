@extends('layouts.admin')
@section('title', 'Quick Absen')
@section('page-title', 'Quick Absen')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    {{-- ═══ KIRI: SCANNER & PREVIEW ═══ --}}
    <div class="lg:col-span-4 space-y-4">

        {{-- Scanner Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.04);">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(16,185,129,0.1);">
                        <i class="fa-solid fa-fingerprint text-emerald-500 text-[13px]"></i>
                    </div>
                    <div>
                        <h3 class="text-[13px] font-700 font-bold text-gray-800 leading-tight">Quick Absen</h3>
                        <p class="text-[10.5px] text-gray-400 mt-0.5">Kode member · nama · no WA</p>
                    </div>
                </div>
            </div>

            {{-- Input area --}}
            <div class="p-5 space-y-3">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-[11px]"></i>
                    <input type="text" id="input_kode" autofocus
                        class="w-full bg-gray-50 border border-gray-200 pl-9 pr-4 py-3 rounded-xl text-[13px] font-mono font-bold text-emerald-600 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none transition uppercase placeholder:text-gray-300 placeholder:font-normal placeholder:normal-case"
                        placeholder="Cari kode / nama / no WA">
                </div>

                <button type="button" onclick="cekMember()" id="btn-cek"
                    class="w-full text-white font-semibold py-2.5 rounded-xl transition flex items-center justify-center gap-2 text-[13px]"
                    style="background: #0f172a;">
                    <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                    Cek Status
                </button>
            </div>

            {{-- List multiple hasil --}}
            <div id="list-multiple" class="hidden px-5 pb-5">
                <div class="border border-dashed border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-3 py-2.5 bg-gray-50 border-b border-gray-100">
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Pilih Member</p>
                    </div>
                    <div id="container-pilihan" class="p-2 space-y-1"></div>
                </div>
            </div>

            {{-- Preview Member --}}
            <div id="preview-member" class="hidden px-5 pb-5">
                <div class="border-t border-dashed border-gray-100 pt-4">
                    <div id="status-container" class="rounded-xl p-4 border">

                        {{-- Avatar + Nama --}}
                        <div class="flex items-center gap-3 mb-4">
                            <div id="p-avatar" class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-[12px] flex-shrink-0">
                                <span id="p-initial">--</span>
                            </div>
                            <div class="min-w-0">
                                <h4 id="p-nama" class="font-bold text-gray-800 text-[13px] leading-tight truncate">--</h4>
                                <p id="p-kode" class="text-[10px] font-mono text-emerald-500 mt-0.5">--</p>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="space-y-2 mb-4 pt-3 border-t border-dashed border-gray-200/80">
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] text-gray-400">Status Akun</span>
                                <span id="p-status" class="text-[11px] font-bold uppercase tracking-wide">--</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] text-gray-400">Masa Berlaku</span>
                                <span id="p-expired" class="text-[11px] font-semibold text-gray-700">--</span>
                            </div>
                        </div>

                        {{-- Action --}}
                        <form id="form-absen" method="POST" action="{{ route('absensi.store') }}" class="hidden">
                            @csrf
                            <input type="hidden" name="member_id" id="p-id">
                            <button type="submit"
                                class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 rounded-xl transition text-[13px] flex items-center justify-center gap-2">
                                <i class="fa-solid fa-check text-[11px]"></i> Konfirmasi Masuk
                            </button>
                        </form>

                        <a id="btn-manage" href="{{ route('transaksi.index') }}"
                            class="hidden w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 rounded-xl transition text-[13px] items-center justify-center gap-2">
                            <i class="fa-solid fa-user-gear text-[11px]"></i>
                            Perpanjang / Edit Member
                        </a>
                    </div>

                    {{-- Double absen warning --}}
                    <div id="msg-double" class="hidden mt-3 flex items-center gap-2 text-[11.5px] text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 flex-shrink-0"></i>
                        <span id="msg-double-text"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ KANAN: LOG ABSENSI ═══ --}}
    <div class="lg:col-span-8 space-y-4">

        {{-- Filter --}}
        <div class="bg-white rounded-2xl border border-gray-100 px-4 py-3.5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <form action="{{ route('absensi.index') }}" method="GET" class="flex flex-wrap gap-2">
                <div class="flex-1 min-w-[160px] relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, kode, atau no WA..."
                        class="w-full bg-gray-50 border border-gray-200 text-[12px] rounded-lg pl-8 pr-3 py-2 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none">
                </div>
                <div>
                    <input type="date" name="date" value="{{ $date }}"
                        class="bg-gray-50 border border-gray-200 text-[12px] rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 outline-none">
                </div>
                <button type="submit"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-[12px] font-semibold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-filter text-[10px]"></i> Filter
                </button>
                @if(request('search') || request('date'))
                <a href="{{ route('absensi.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-4 py-2 rounded-lg text-[12px] font-semibold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-xmark text-[10px]"></i> Reset
                </a>
                @endif
            </form>
        </div>

        {{-- Log Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.04);">

            {{-- Table header --}}
            <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-800 text-[13px]">Log Absensi</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                        <span class="mx-1 text-gray-200">·</span>
                        <span class="font-semibold text-emerald-600">{{ $totalHariIni }} orang masuk</span>
                    </p>
                </div>
                <div class="flex items-center gap-1.5 text-[10.5px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                    LIVE
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/70">
                            <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Member</th>
                            <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Waktu Masuk</th>
                            <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($absensi as $row)
                        <tr class="hover:bg-gray-50/60 transition-colors group">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-emerald-600 text-[10px] flex-shrink-0 transition"
                                         style="background: rgba(16,185,129,0.08);">
                                        {{ strtoupper(substr($row->member->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-[12.5px] font-semibold text-gray-800 leading-tight">{{ $row->member->nama }}</div>
                                        <div class="text-[10px] font-mono text-gray-400 mt-0.5">{{ $row->member->kode_member }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="text-[13px] font-bold text-gray-700">{{ $row->created_at->format('H:i') }}</span>
                                <span class="text-[10px] text-gray-400 block mt-0.5">{{ $row->created_at->format('d/m/y') }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[9.5px] font-bold uppercase tracking-wide"
                                      style="background: rgba(16,185,129,0.1); color: #059669;">
                                    <i class="fa-solid fa-check text-[8px]"></i> Berhasil
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-16 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-calendar-xmark text-xl text-gray-300"></i>
                                </div>
                                <p class="text-[12px] text-gray-400 font-medium">Belum ada absensi untuk tanggal ini</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($absensi->hasPages())
            <div class="px-5 py-3 border-t border-gray-50">
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

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-[11px]"></i> Mencari...';
        listContainer.classList.add('hidden');
        preview.classList.add('hidden');

        fetch(`{{ route('absensi.cek') }}?keyword=${encodeURIComponent(kode)}`, {
            headers: { 'Accept': 'application/json', 'ngrok-skip-browser-warning': 'true' }
        })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(res => {
            if (res.multiple) {
                listContainer.classList.remove('hidden');
                containerPilihan.innerHTML = '';
                res.data.forEach(m => {
                    const b = document.createElement('button');
                    b.className = 'w-full text-left px-3 py-2.5 rounded-lg hover:bg-gray-50 flex justify-between items-center transition gap-3';
                    b.innerHTML = `
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold text-emerald-600 flex-shrink-0" style="background:rgba(16,185,129,0.1);">${m.nama.substring(0,2).toUpperCase()}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[12px] font-semibold text-gray-800 truncate">${m.nama}</div>
                            <div class="text-[10px] font-mono text-gray-400">${m.kode_member}</div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 flex-shrink-0"></i>`;
                    b.onclick = () => { document.getElementById('input_kode').value = m.kode_member; cekMember(); };
                    containerPilihan.appendChild(b);
                });
                return;
            }

            if (!res.success) { Swal.toast(res.message, 'error'); return; }

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
                statusEl.innerText = d.is_expired ? 'Membership Expired' : 'Non-Aktif';
                statusEl.className = 'text-[11px] font-bold uppercase tracking-wide text-red-500';
                container.className = 'rounded-xl p-4 border bg-red-50/60 border-red-100';
                avatar.className = 'w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-[12px] flex-shrink-0 bg-red-400';
                btnManage.classList.remove('hidden');
                btnManage.classList.add('flex');
                btnManage.href = d.manage_url;
            } else {
                statusEl.innerText = 'Aktif';
                statusEl.className = 'text-[11px] font-bold uppercase tracking-wide text-emerald-600';
                container.className = 'rounded-xl p-4 border bg-emerald-50/60 border-emerald-100';
                avatar.className = 'w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-[12px] flex-shrink-0 bg-emerald-500';

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
        .catch(err => Swal.toast('Gagal: ' + err.message, 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cek Status';
        });
    }

    document.getElementById('input_kode').addEventListener('keypress', e => {
        if (e.key === 'Enter') cekMember();
    });
</script>
@endpush