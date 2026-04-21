{{--
Partial: _upload_form.blade.php
Dipanggil di pembayaran.blade.php untuk STATE 1 (upload) dan STATE 3 (ditolak)

Props:
- $kode : kode_invoice transaksi
- $verifikasi : object verifikasi (nullable)
--}}

<form method="POST" action="/pembayaran/{{ $kode }}/upload" enctype="multipart/form-data" id="uploadForm">
    @csrf

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:1rem;">
            @foreach($errors->all() as $e)
                {{ $e }}<br>
            @endforeach
        </div>
    @endif

    <div class="form-field">
        <label class="field-label">Bank / Dompet Pengirim</label>
        <div class="bank-select-wrap">
            <select name="nama_bank" class="field-input" required>
                <option value="">— Pilih bank —</option>
                <optgroup label="Bank ">
                    @foreach(['Bank BRI', 'Bank BNI', 'Bank Mandiri', 'Bank BTN', 'Bank BCA', 'Bank CIMB Niaga'] as $b)
                        <option {{ old('nama_bank') == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="E-Wallet / Dompet Digital">
                    @foreach(['GoPay', 'OVO', 'DANA', 'ShopeePay', 'LinkAja'] as $b)
                        <option {{ old('nama_bank') == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>
    </div>


    <div class="form-field">
        <label class="field-label">Nama Pemilik Rekening</label>
        <input type="text" name="nama_rekening" class="field-input" placeholder="Sesuai nama di rekening"
            value="{{ old('nama_rekening') }}" required>
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
    const input = document.getElementById('fileInput');
    const preview = document.getElementById('previewImg');
    const fileName = document.getElementById('fileName');

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Validasi ukuran (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File terlalu besar! Maksimal 2MB.');
            this.value = '';
            return;
        }

        // Preview
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(file);

        fileName.textContent = file.name;
    });
    // Prevent double submit
    document.getElementById('uploadForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        const txt = document.getElementById('submitText');
        const spin = document.getElementById('submitSpinner');
        btn.disabled = true;
        txt.style.display = 'none';
        spin.style.display = 'inline';
    });
</script>