@extends('layouts.admin')
@section('title', 'Manajemen Paket')
@section('page-title', 'Manajemen Paket')

@section('content')

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    {{-- Header --}}
    <div class="p-6 border-b border-gray-50">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked text-gray-500 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg text-gray-800">Daftar Paket Layanan</h3>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $paket->count() }} paket tersedia</p>
                </div>
            </div>
            <button onclick="document.getElementById('modal-tambah-paket').classList.remove('hidden')"
                class="px-4 py-2.5 text-sm bg-white border border-gray-200 hover:border-gray-300 text-gray-700 rounded-xl font-medium transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                <i class="fa-solid fa-plus text-sm"></i>
                Tambah Paket
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Paket</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Deskripsi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Durasi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Harga</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($paket as $p)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-box text-emerald-600 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-800">{{ $p->nama_paket }}</span>
                                <p class="text-xs text-gray-500 mt-0.5">ID: {{ $p->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-sm text-gray-600 line-clamp-2 max-w-md">{{ $p->deskripsi ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="inline-flex px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full">
                            {{ $p->durasi_hari }} Hari
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($p->harga) }}</span>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <div class="flex justify-end gap-1.5">
                            <button onclick="openEditPaket({{ $p->id }}, '{{ $p->nama_paket }}', {{ $p->harga }}, {{ $p->durasi_hari }}, '{{ $p->deskripsi }}')"
                                class="p-2.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all duration-200">
                                <i class="fa-solid fa-pen text-sm"></i>
                            </button>
                            <form method="POST" action="{{ route('paket.destroy', $p->id) }}" onsubmit="return confirm('Hapus paket ini?')" class="inline-block">
                                @csrf @method('DELETE')
                                <button class="p-2.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all duration-200">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-box-open text-gray-400 text-lg"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Belum ada paket</h3>
                        <p class="text-sm text-gray-400">Tambahkan paket pertama Anda</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modal-tambah-paket" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/20 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm border border-gray-100 animate-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-50">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-plus text-emerald-600 text-sm"></i>
                </div>
                <h2 class="font-semibold text-lg text-gray-800">Tambah Paket</h2>
            </div>
            <button onclick="document.getElementById('modal-tambah-paket').classList.add('hidden')"
                class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('paket.store') }}" class="p-6 space-y-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Nama Paket</label>
                    <input type="text" name="nama_paket" required
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition-all duration-200 bg-gray-50 hover:bg-gray-50/80">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Deskripsi</label>
                    <input type="text" name="deskripsi"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition-all duration-200 bg-gray-50 hover:bg-gray-50/80">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Harga</label>
                        <input type="number" name="harga" required
                            class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition-all duration-200 bg-gray-50 hover:bg-gray-50/80 text-right">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Durasi</label>
                        <input type="number" name="durasi_hari" required
                            class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition-all duration-200 bg-gray-50 hover:bg-gray-50/80">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-tambah-paket').classList.add('hidden')"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-200">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modal-edit-paket" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/20 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm border border-gray-100 animate-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-50">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-pen text-blue-600 text-sm"></i>
                </div>
                <h2 class="font-semibold text-lg text-gray-800">Edit Paket</h2>
            </div>
            <button onclick="document.getElementById('modal-edit-paket').classList.add('hidden')"
                class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="form-edit-paket" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Nama Paket</label>
                    <input type="text" name="nama_paket" id="edit-nama" required
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all duration-200 bg-gray-50 hover:bg-gray-50/80">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Deskripsi</label>
                    <input type="text" name="deskripsi" id="edit-desc"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all duration-200 bg-gray-50 hover:bg-gray-50/80">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Harga</label>
                        <input type="number" name="harga" id="edit-harga" required
                            class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all duration-200 bg-gray-50 hover:bg-gray-50/80 text-right">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Durasi</label>
                        <input type="number" name="durasi_hari" id="edit-durasi" required
                            class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all duration-200 bg-gray-50 hover:bg-gray-50/80">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-paket').classList.add('hidden')"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-200">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold bg-blue-500 hover:bg-blue-600 text-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEditPaket(id, nama, harga, durasi, desc) {
    document.getElementById('form-edit-paket').action = `/paket/${id}`;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-harga').value = harga;
    document.getElementById('edit-durasi').value = durasi;
    document.getElementById('edit-desc').value = desc;
    document.getElementById('modal-edit-paket').classList.remove('hidden');
}

// Close modals on escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.getElementById('modal-tambah-paket')?.classList.add('hidden');
        document.getElementById('modal-edit-paket')?.classList.add('hidden');
    }
});
</script>
@endpush