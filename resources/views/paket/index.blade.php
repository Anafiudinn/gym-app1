@extends('layouts.admin')

@section('title', 'Manajemen Paket')
@section('page-title', 'Manajemen Paket')

@push('styles')
<style>
    /* ── Card base ── */
    .card { background:#fff; border-radius:12px; border:1px solid #e9ecf0; }

    /* ── Badge ── */
    .badge {
        display:inline-flex; align-items:center; gap:4px;
        padding:3px 10px; border-radius:99px;
        font-size:11px; font-weight:600;
    }
    .badge-green  { background:#f0fdf7; color:#059669; }
    .badge-blue   { background:#eff6ff; color:#2563eb; }
    .badge-orange { background:#fff7ed; color:#d97706; }

    /* ── Button ── */
    .btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:7px 14px; border-radius:8px;
        font-size:12.5px; font-weight:600;
        transition:all .15s; cursor:pointer; border:none;
    }
    .btn-primary       { background:#10b981; color:#fff; }
    .btn-primary:hover { background:#059669; }
    .btn-white         { background:#fff; color:#374151; border:1px solid #e5e7eb; }
    .btn-white:hover   { background:#f9fafb; }
    .btn-danger        { background:#fef2f2; color:#dc2626; }
    .btn-danger:hover  { background:#fee2e2; }
    .btn-edit          { background:#eff6ff; color:#2563eb; }
    .btn-edit:hover    { background:#dbeafe; }
    .btn-sm            { padding:5px 10px; font-size:11.5px; border-radius:7px; }

    /* ── Table ── */
    .tbl th {
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.07em; color:#9ca3af;
        padding:10px 14px; border-bottom:1px solid #f1f3f5;
        white-space:nowrap;
    }
    .tbl td {
        padding:11px 14px; font-size:13px; color:#374151;
        border-bottom:1px solid #f8f9fa; vertical-align:middle;
    }
    .tbl tr:last-child td { border-bottom:none; }
    .tbl tr:hover td { background:#fafafa; }

    /* ── Modal ── */
    .modal-backdrop {
        position:fixed; inset:0; background:rgba(0,0,0,.45);
        backdrop-filter:blur(4px); z-index:200;
        display:flex; align-items:center; justify-content:center;
        padding:16px;
    }
    .modal-box {
        background:#fff; border-radius:14px; width:100%; max-width:440px;
        box-shadow:0 20px 60px rgba(0,0,0,.15);
        animation:modalIn .2s ease;
    }
    @keyframes modalIn {
        from { opacity:0; transform:translateY(12px) scale(.98); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    .modal-header {
        display:flex; align-items:center; justify-content:space-between;
        padding:18px 20px 14px; border-bottom:1px solid #f1f3f5;
    }
    .modal-body   { padding:18px 20px; }
    .modal-footer { padding:12px 20px; border-top:1px solid #f1f3f5; display:flex; justify-content:flex-end; gap:8px; }

    /* ── Fields ── */
    .field-group  { margin-bottom:14px; }
    .field-label  { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px; }
    .field-input  {
        width:100%; padding:8px 11px; border-radius:8px;
        border:1px solid #e5e7eb; font-size:13px; color:#111827;
        background:#f9fafb; transition:border .15s, box-shadow .15s; outline:none;
    }
    .field-input:focus { border-color:#10b981; background:#fff; box-shadow:0 0 0 3px rgba(16,185,129,.1); }
    .field-hint        { font-size:11px; color:#9ca3af; margin-top:4px; }
    .field-input.error { border-color:#ef4444; background:#fef2f2; }

    /* ── Stat cards ── */
    .stat-card {
        background:#fff; border:1px solid #e9ecf0; border-radius:12px;
        padding:16px 18px; display:flex; align-items:center; gap:14px;
    }
    .stat-icon {
        width:40px; height:40px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:16px; flex-shrink:0;
    }
    .stat-label { font-size:11.5px; color:#9ca3af; font-weight:500; }
    .stat-value { font-size:22px; font-weight:700; color:#111827; line-height:1.1; }

    /* ── Empty state ── */
    .empty-state { text-align:center; padding:48px 24px; }
    .empty-state i { font-size:36px; color:#d1d5db; }
    .empty-state p { font-size:13px; color:#9ca3af; margin-top:10px; }

    /* ── Search ── */
    .search-wrap   { position:relative; }
    .search-wrap i { position:absolute; left:10px; top:50%; transform:translateY(-50%); font-size:12px; color:#9ca3af; }
    .search-input  { padding-left:30px; }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-[17px] font-bold text-gray-800 leading-tight">Manajemen Paket</h1>
        <p class="text-[12px] text-gray-400 mt-0.5">Kelola paket membership gym Anda</p>
    </div>
    <button onclick="openModal('modal-tambah')" class="btn btn-primary">
        <i class="fa-solid fa-plus text-[11px]"></i> Tambah Paket
    </button>
</div>

{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    <div class="stat-card">
        <div class="stat-icon bg-emerald-50">
            <i class="fa-solid fa-box-open text-emerald-500"></i>
        </div>
        <div>
            <div class="stat-label">Total Paket</div>
            <div class="stat-value">{{ $paket->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-blue-50">
            <i class="fa-solid fa-tag text-blue-500"></i>
        </div>
        <div>
            <div class="stat-label">Harga Terendah</div>
            <div class="stat-value text-[15px] mt-0.5">
                Rp {{ $paket->count() ? number_format($paket->min('harga'),0,',','.') : '—' }}
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-orange-50">
            <i class="fa-solid fa-crown text-orange-400"></i>
        </div>
        <div>
            <div class="stat-label">Harga Tertinggi</div>
            <div class="stat-value text-[15px] mt-0.5">
                Rp {{ $paket->count() ? number_format($paket->max('harga'),0,',','.') : '—' }}
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple-50">
            <i class="fa-solid fa-calendar-days text-purple-500"></i>
        </div>
        <div>
            <div class="stat-label">Durasi Terlama</div>
            <div class="stat-value text-[15px] mt-0.5">
                {{ $paket->count() ? $paket->max('durasi_hari').' hari' : '—' }}
            </div>
        </div>
    </div>
</div>

{{-- ── Table Card ── --}}
<div class="card">

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-b border-gray-100">
        <div class="font-semibold text-[13.5px] text-gray-700">
            Daftar Paket
            <span class="ml-1.5 text-[11px] font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                {{ $paket->count() }} paket
            </span>
        </div>
        <div class="search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" onkeyup="filterTable()"
                   placeholder="Cari paket..."
                   class="field-input search-input w-full sm:w-52 text-[12.5px]">
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="tbl w-full" id="paketTable">
            <thead>
                <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">Nama Paket</th>
                    <th class="text-left">Harga</th>
                    <th class="text-left">Durasi</th>
                    <th class="text-left">Kategori</th>
                    <th class="text-left">Dibuat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="paketBody">
                @forelse($paket as $i => $p)
                <tr class="paket-row">
                    <td class="text-gray-400 text-[12px]">{{ $i + 1 }}</td>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-dumbbell text-emerald-500 text-[11px]"></i>
                            </div>
                            <span class="font-semibold text-gray-800 text-[13px] paket-name">{{ $p->nama_paket }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($p->harga,0,',','.') }}</span>
                    </td>
                    <td>
                        <div class="flex items-center gap-1.5 text-gray-600 text-[12.5px]">
                            <i class="fa-regular fa-clock text-gray-400 text-[11px]"></i>
                            {{ $p->durasi_hari }} hari
                        </div>
                    </td>
                    <td>
                        @if($p->durasi_hari <= 7)
                            <span class="badge badge-orange"><i class="fa-solid fa-bolt text-[9px]"></i> Trial</span>
                        @elseif($p->durasi_hari <= 31)
                            <span class="badge badge-blue"><i class="fa-solid fa-star text-[9px]"></i> Bulanan</span>
                        @else
                            <span class="badge badge-green"><i class="fa-solid fa-crown text-[9px]"></i> Premium</span>
                        @endif
                    </td>
                    <td class="text-gray-400 text-[12px]">
                        {{ $p->created_at ? $p->created_at->format('d M Y') : '—' }}
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="openEdit({{ $p->id }}, '{{ addslashes($p->nama_paket) }}', {{ $p->harga }}, {{ $p->durasi_hari }})"
                                    class="btn btn-edit btn-sm" title="Edit">
                                <i class="fa-solid fa-pen text-[10px]"></i> Edit
                            </button>
                            <form id="form-del-{{ $p->id }}"
                                  action="{{ route('paket.destroy', $p->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="Swal.deleteConfirm(document.getElementById('form-del-{{ $p->id }}'), '{{ addslashes($p->nama_paket) }}')"
                                        class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="empty-row">
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fa-solid fa-box-open"></i>
                            <p>Belum ada paket. Tambahkan paket pertama Anda!</p>
                            <button onclick="openModal('modal-tambah')" class="btn btn-primary mt-4">
                                <i class="fa-solid fa-plus text-[11px]"></i> Tambah Paket
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div id="no-results" class="hidden">
            <div class="empty-state">
                <i class="fa-solid fa-magnifying-glass"></i>
                <p>Tidak ada paket yang cocok dengan pencarian Anda.</p>
            </div>
        </div>
    </div>
</div>


{{-- ═══ MODAL TAMBAH ═══ --}}
<div id="modal-tambah" class="modal-backdrop hidden" onclick="closeOnBackdrop(event,'modal-tambah')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <h3 class="font-bold text-[15px] text-gray-800">Tambah Paket</h3>
                <p class="text-[11.5px] text-gray-400 mt-0.5">Isi detail paket baru</p>
            </div>
            <button onclick="closeModal('modal-tambah')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark text-[13px]"></i>
            </button>
        </div>
        <form action="{{ route('paket.store') }}" method="POST" id="form-tambah" novalidate>
            @csrf
            <div class="modal-body">
                <div class="field-group">
                    <label class="field-label">Nama Paket <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_paket" id="tambah-nama"
                           placeholder="cth. Paket Silver 1 Bulan"
                           class="field-input" autocomplete="off">
                    <div class="field-hint hidden text-red-500" id="err-tambah-nama">Nama paket wajib diisi.</div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="field-group mb-0">
                        <label class="field-label">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="harga" id="tambah-harga" placeholder="150000" min="0"
                               class="field-input" oninput="previewHarga(this,'preview-harga-tambah')">
                        <div class="field-hint" id="preview-harga-tambah"></div>
                        <div class="field-hint hidden text-red-500" id="err-tambah-harga">Harga wajib diisi.</div>
                    </div>
                    <div class="field-group mb-0">
                        <label class="field-label">Durasi (Hari) <span class="text-red-500">*</span></label>
                        <input type="number" name="durasi_hari" id="tambah-durasi" placeholder="30" min="1"
                               class="field-input" oninput="previewDurasi(this,'preview-durasi-tambah')">
                        <div class="field-hint" id="preview-durasi-tambah"></div>
                        <div class="field-hint hidden text-red-500" id="err-tambah-durasi">Durasi wajib diisi.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-tambah')" class="btn btn-white">Batal</button>
                <button type="button" onclick="submitTambah()" class="btn btn-primary">
                    <i class="fa-solid fa-check text-[11px]"></i> Simpan Paket
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ═══ MODAL EDIT ═══ --}}
<div id="modal-edit" class="modal-backdrop hidden" onclick="closeOnBackdrop(event,'modal-edit')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <h3 class="font-bold text-[15px] text-gray-800">Edit Paket</h3>
                <p class="text-[11.5px] text-gray-400 mt-0.5">Ubah detail paket</p>
            </div>
            <button onclick="closeModal('modal-edit')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark text-[13px]"></i>
            </button>
        </div>
        <form id="form-edit" method="POST" novalidate>
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="field-group">
                    <label class="field-label">Nama Paket <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_paket" id="edit-nama"
                           placeholder="Nama paket" class="field-input" autocomplete="off">
                    <div class="field-hint hidden text-red-500" id="err-edit-nama">Nama paket wajib diisi.</div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="field-group mb-0">
                        <label class="field-label">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="harga" id="edit-harga" placeholder="150000" min="0"
                               class="field-input" oninput="previewHarga(this,'preview-harga-edit')">
                        <div class="field-hint" id="preview-harga-edit"></div>
                        <div class="field-hint hidden text-red-500" id="err-edit-harga">Harga wajib diisi.</div>
                    </div>
                    <div class="field-group mb-0">
                        <label class="field-label">Durasi (Hari) <span class="text-red-500">*</span></label>
                        <input type="number" name="durasi_hari" id="edit-durasi" placeholder="30" min="1"
                               class="field-input" oninput="previewDurasi(this,'preview-durasi-edit')">
                        <div class="field-hint" id="preview-durasi-edit"></div>
                        <div class="field-hint hidden text-red-500" id="err-edit-durasi">Durasi wajib diisi.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-edit')" class="btn btn-white">Batal</button>
                <button type="button" onclick="submitEdit()" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk text-[11px]"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Modal helpers ── */
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
    clearErrors(id);
}
function closeOnBackdrop(e, id) {
    if (e.target === document.getElementById(id)) closeModal(id);
}

/* ── Open edit ── */
function openEdit(id, nama, harga, durasi) {
    const form = document.getElementById('form-edit');
    form.action = `/paket/${id}`;
    document.getElementById('edit-nama').value   = nama;
    document.getElementById('edit-harga').value  = harga;
    document.getElementById('edit-durasi').value = durasi;
    previewHarga(document.getElementById('edit-harga'), 'preview-harga-edit');
    previewDurasi(document.getElementById('edit-durasi'), 'preview-durasi-edit');
    openModal('modal-edit');
}

/* ── Preview helpers ── */
function previewHarga(el, previewId) {
    const v = parseInt(el.value);
    document.getElementById(previewId).textContent = v > 0 ? 'Rp ' + v.toLocaleString('id-ID') : '';
}
function previewDurasi(el, previewId) {
    const v = parseInt(el.value);
    const p = document.getElementById(previewId);
    if (!v || v < 1) { p.textContent = ''; return; }
    const bulan = Math.floor(v / 30);
    const sisa  = v % 30;
    p.textContent = bulan > 0 ? `${bulan} bulan${sisa > 0 ? ` ${sisa} hari` : ''}` : `${v} hari`;
}

/* ── Validation ── */
function clearErrors(modalId) {
    document.getElementById(modalId).querySelectorAll('.field-hint.text-red-500').forEach(el => el.classList.add('hidden'));
    document.getElementById(modalId).querySelectorAll('.field-input.error').forEach(el => el.classList.remove('error'));
}
function showErr(inputId, errId) {
    document.getElementById(inputId).classList.add('error');
    document.getElementById(errId).classList.remove('hidden');
}
function clearErr(inputId, errId) {
    document.getElementById(inputId).classList.remove('error');
    document.getElementById(errId).classList.add('hidden');
}
function validate(prefix) {
    let ok = true;
    const nama   = document.getElementById(`${prefix}-nama`).value.trim();
    const harga  = document.getElementById(`${prefix}-harga`).value;
    const durasi = document.getElementById(`${prefix}-durasi`).value;
    if (!nama)              { showErr(`${prefix}-nama`,   `err-${prefix}-nama`);   ok = false; }
    else                    { clearErr(`${prefix}-nama`,  `err-${prefix}-nama`); }
    if (!harga || harga<=0) { showErr(`${prefix}-harga`,  `err-${prefix}-harga`);  ok = false; }
    else                    { clearErr(`${prefix}-harga`, `err-${prefix}-harga`); }
    if (!durasi||durasi<=0) { showErr(`${prefix}-durasi`, `err-${prefix}-durasi`); ok = false; }
    else                    { clearErr(`${prefix}-durasi`,`err-${prefix}-durasi`); }
    return ok;
}

/* ── Submit tambah ── */
function submitTambah() {
    if (!validate('tambah')) { Swal.toast('Lengkapi semua field yang wajib diisi.', 'warning'); return; }
    Swal.confirm({
        title: 'Tambah Paket?',
        text: `Paket "${document.getElementById('tambah-nama').value}" akan disimpan.`,
        confirmText: 'Ya, Simpan',
        onConfirm: () => document.getElementById('form-tambah').submit()
    });
}

/* ── Submit edit ── */
function submitEdit() {
    if (!validate('edit')) { Swal.toast('Lengkapi semua field yang wajib diisi.', 'warning'); return; }
    Swal.confirm({
        title: 'Simpan Perubahan?',
        text: `Data paket "${document.getElementById('edit-nama').value}" akan diperbarui.`,
        confirmText: 'Ya, Simpan',
        onConfirm: () => document.getElementById('form-edit').submit()
    });
}

/* ── Search ── */
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.paket-row');
    let found = 0;
    rows.forEach(row => {
        const show = row.querySelector('.paket-name').textContent.toLowerCase().includes(q);
        row.style.display = show ? '' : 'none';
        if (show) found++;
    });
    document.getElementById('no-results').classList.toggle('hidden', found > 0);
}

/* ── ESC to close ── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modal-tambah','modal-edit'].forEach(id => {
            if (!document.getElementById(id).classList.contains('hidden')) closeModal(id);
        });
    }
});
</script>
@endpush