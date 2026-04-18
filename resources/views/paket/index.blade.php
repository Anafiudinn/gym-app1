@extends('layouts.admin')
@section('title', 'Manajemen Paket')
@section('page-title', 'Manajemen Paket')

@push('styles')
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-up { animation: fadeUp 0.35s ease both; }
    .fade-up-1 { animation-delay: .05s; }
    .fade-up-2 { animation-delay: .10s; }
    
    .paket-row { transition: all 0.2s ease; }
    .paket-row:hover { transform: translateY(-1px); }
    .paket-row:hover .row-actions { opacity: 1; }
    .row-actions { opacity: 0; transition: opacity 0.15s; }
    
    .modal-enter { animation: fadeUp 0.25s ease-out; }
</style>
@endpush

@push('scripts')
<script>
let currentEditId = null;

// Auto format Rupiah
function formatRupiah(input) {
    let value = input.value.replace(/[^\d]/g, '');
    value = value ? parseInt(value).toLocaleString('id-ID') : '';
    input.value = 'Rp ' + value;
}

// Remove format untuk submit
function removeRupiahFormat(input) {
    input.value = input.value.replace(/[^\d]/g, '');
}

// Open Edit Paket
function openEditPaket(id, nama, harga, durasi, desc) {
    currentEditId = id;
    document.getElementById('form-edit-paket').action = `/paket/${id}`;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-harga').value = parseInt(harga).toLocaleString('id-ID');
    document.getElementById('edit-durasi').value = durasi;
    document.getElementById('edit-desc').value = desc || '';
    document.getElementById('modal-edit-paket').classList.remove('hidden');
    document.getElementById('modal-edit-paket').classList.add('modal-enter');
}

// ✅ SWEETALERT2 DELETE PAKET
async function handleDeletePaket(event, paketId, namaPaket) {
    event.preventDefault();
    
    const confirmed = await GymProAlert.confirm(
        'Hapus Paket',
        `Paket "${namaPaket}" akan dihapus permanen dan tidak bisa dipulihkan. Pastikan tidak ada member aktif yang menggunakan paket ini!`,
        'Hapus Paket',
        'Batal'
    );
    
    if (confirmed) {
        event.target.closest('form').submit();
    }
}

// Close modals
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// ESC key close
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal('modal-tambah-paket');
        closeModal('modal-edit-paket');
    }
});
</script>
@endpush

@section('content')
<div class="space-y-6 fade-up fade-up-1">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-boxes-stacked text-emerald-500 text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-800 leading-tight">Manajemen Paket</h1>
                <p class="text-xs text-gray-400 mt-0.5">{{ $paket->count() }} paket tersedia</p>
            </div>
        </div>
       <!-- BENAR ✅ -->
<button onclick="document.getElementById('modal-tambah-paket').classList.remove('hidden')"
    class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all flex items-center gap-2">
    <i class="fa-solid fa-plus"></i>Tambah Paket
</button>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden fade-up fade-up-2">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/70">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-left">Paket</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-left">Deskripsi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Durasi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Harga</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($paket as $p)
                    <tr class="paket-row h-16 hover:bg-emerald-50/30 transition-all">
                        {{-- Paket --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-box text-emerald-600 text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $p->nama_paket }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">ID: #{{ $p->id }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Deskripsi --}}
                        <td class="px-6 py-4">
                            <p class="text-[12px] text-gray-600 line-clamp-2 max-w-md">{{ $p->deskripsi ?? '-' }}</p>
                        </td>

                        {{-- Durasi --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-3 py-1.5 bg-emerald-50 text-emerald-700 text-[11px] font-semibold rounded-full border border-emerald-200">
                                {{ $p->durasi_hari }} Hari
                            </span>
                        </td>

                        {{-- Harga --}}
                        <td class="px-6 py-4 text-right">
                            <p class="text-[14px] font-black text-gray-800">Rp {{ number_format($p->harga) }}</p>
                            <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">/ {{ $p->durasi_hari }} hari</p>
                        </td>

                        {{-- Aksi - ✅ SWEETALERT2 INTEGRATED --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 row-actions">
                                {{-- Edit --}}
                                <button onclick="openEditPaket({{ $p->id }}, '{{ addslashes($p->nama_paket) }}', {{ $p->harga }}, {{ $p->durasi_hari }}, '{{ addslashes($p->deskripsi ?? '') }}')"
                                    class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all group shadow-sm hover:shadow-md"
                                    title="Edit Paket">
                                    <i class="fa-solid fa-pen text-[11px] group-hover:scale-110"></i>
                                </button>
                                
                                {{-- Delete - SweetAlert2 ✅ --}}
                                <form method="POST" action="{{ route('paket.destroy', $p->id) }}" 
                                      onsubmit="handleDeletePaket(event, {{ $p->id }}, '{{ addslashes($p->nama_paket) }}')"
                                      class="inline-flex">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all group shadow-sm hover:shadow-md"
                                        title="Hapus Paket">
                                        <i class="fa-solid fa-trash text-[11px] group-hover:scale-110"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-300">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center">
                                    <i class="fa-solid fa-box-open text-gray-400 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Belum ada paket tersedia</p>
                                    <p class="text-[11px] text-gray-300 mt-1">Tambahkan paket pertama Anda</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- =========================================== --}}
{{-- MODAL TAMBAH PAKET --}}
<div id="modal-tambah-paket" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 flex items-center justify-between px-6 py-5 border-b border-gray-50 bg-white/80 backdrop-blur-sm z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-plus text-emerald-500 text-sm"></i>
                </div>
                <h2 class="font-bold text-lg text-gray-800">Tambah Paket Baru</h2>
            </div>
            <button onclick="closeModal('modal-tambah-paket')"
                class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg transition-all hover:bg-gray-100">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('paket.store') }}" class="p-6 space-y-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Nama Paket *</label>
                    <input type="text" name="nama_paket" required maxlength="50"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none transition-all bg-gray-50 hover:bg-white">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Deskripsi</label>
                    <input type="text" name="deskripsi" maxlength="100"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none transition-all bg-gray-50 hover:bg-white">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Harga *</label>
                        <input type="text" name="harga" id="harga-tambah" required 
                            oninput="formatRupiah(this)" onblur="removeRupiahFormat(this)"
                            class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none transition-all bg-gray-50 hover:bg-white text-right font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Durasi *</label>
                        <input type="number" name="durasi_hari" min="1" max="365" required
                            class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none transition-all bg-gray-50 hover:bg-white">
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('modal-tambah-paket')"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-sm hover:shadow-md transition-all">
                    <i class="fa-solid fa-save mr-1.5"></i>Simpan Paket
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PAKET --}}
<div id="modal-edit-paket" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 flex items-center justify-between px-6 py-5 border-b border-gray-50 bg-white/80 backdrop-blur-sm z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-pen text-blue-500 text-sm"></i>
                </div>
                <h2 class="font-bold text-lg text-gray-800">Edit Paket</h2>
            </div>
            <button onclick="closeModal('modal-edit-paket')"
                class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg transition-all hover:bg-gray-100">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <form id="form-edit-paket" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Nama Paket *</label>
                    <input type="text" name="nama_paket" id="edit-nama" required maxlength="50"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition-all bg-gray-50 hover:bg-white">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Deskripsi</label>
                    <input type="text" name="deskripsi" id="edit-desc" maxlength="100"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition-all bg-gray-50 hover:bg-white">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Harga *</label>
                        <input type="text" name="harga" id="edit-harga" required 
                            oninput="formatRupiah(this)" onblur="removeRupiahFormat(this)"
                            class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition-all bg-gray-50 hover:bg-white text-right font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Durasi *</label>
                        <input type="number" name="durasi_hari" id="edit-durasi" min="1" max="365" required
                            class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition-all bg-gray-50 hover:bg-white">
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('modal-edit-paket')"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold bg-blue-500 hover:bg-blue-600 text-white rounded-xl shadow-sm hover:shadow-md transition-all">
                    <i class="fa-solid fa-save mr-1.5"></i>Update Paket
                </button>
            </div>
        </form>
    </div>
</div>

@endsection