{{--
    Partial: _upload_form.blade.php
    Dipanggil di pembayaran.blade.php untuk STATE 1 (upload) dan STATE 3 (ditolak)

    Props:
    - $kode       : kode_invoice transaksi
    - $verifikasi : object verifikasi (nullable)
--}}

<form
    method="POST"
    action="/pembayaran/{{ $kode }}/upload"
    enctype="multipart/form-data"
    id="uploadForm"
>
    @csrf

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:1rem;">
            @foreach($errors->all() as $e)
                {{ $e }}<br>
            @endforeach
        </div>
    @endif

    {{-- Nama Rekening --}}
    <div style="margin-bottom:0.875rem;">
        <label class="field-label">Nama Rekening Pengirim</label>
        <input
            type="text"
            name="nama_rekening"
            class="field-input"
            placeholder="Sesuai nama di rekening bank"
            value="{{ old('nama_rekening', $verifikasi->nama_rekening ?? '') }}"
            required
            autocomplete="name"
        >
    </div>

    {{-- Nama Bank --}}
    <div style="margin-bottom:0.875rem;">
        <label class="field-label">Nama Bank</label>
        <input
            type="text"
            name="nama_bank"
            class="field-input"
            placeholder="Contoh: BCA, BNI, Mandiri, GoPay"
            value="{{ old('nama_bank', $verifikasi->nama_bank ?? '') }}"
            required
            autocomplete="off"
        >
    </div>

    {{-- Upload bukti --}}
    <div style="margin-bottom:1rem;">
        <label class="field-label">Bukti Transfer</label>
        <div class="upload-zone" id="uploadZone">
            <input type="file" name="bukti" id="fileInput" accept="image/jpeg,image/png,image/jpg" required>

            <div class="upload-icon-wrap">↑</div>
            <p>Tap untuk pilih gambar</p>
            <p class="hint">JPG / PNG · Maks 2MB</p>
            <div class="file-name" id="fileName"></div>
        </div>

        {{-- Preview --}}
        <img id="previewImg" class="preview-img" alt="Preview bukti transfer">

        {{-- Kalau sudah ada bukti lama (state ditolak) --}}
        @if($verifikasi && $verifikasi->bukti_pembayaran)
            <p style="font-size:0.73rem;color:var(--muted);margin-top:0.5rem;">
                Bukti sebelumnya sudah tersimpan. Upload baru untuk mengganti.
            </p>
        @endif
    </div>

    {{-- Submit --}}
    <button type="submit" class="btn-submit" id="submitBtn">
        <span id="submitText">
            {{ $verifikasi ? 'Upload Ulang Bukti ↑' : 'Kirim Bukti Pembayaran ↑' }}
        </span>
        <span id="submitSpinner" style="display:none;">Mengirim...</span>
    </button>
</form>

<script>
    // Prevent double submit
    document.getElementById('uploadForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const txt = document.getElementById('submitText');
        const spin = document.getElementById('submitSpinner');
        btn.disabled = true;
        txt.style.display = 'none';
        spin.style.display = 'inline';
    });
</script>