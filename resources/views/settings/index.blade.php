@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@push('styles')
<style>
    /* ── Card base ── */
    .card { background:#fff; border-radius:12px; border:1px solid #e9ecf0; }

    /* ── Section header ── */
    .card-section-header {
        display:flex; align-items:center; gap:9px;
        padding:16px 20px 14px; border-bottom:1px solid #f1f3f5;
    }
    .card-section-icon {
        width:32px; height:32px; border-radius:8px;
        display:flex; align-items:center; justify-content:center;
        font-size:13px; flex-shrink:0;
    }
    .card-section-title  { font-size:13.5px; font-weight:700; color:#111827; }
    .card-section-sub    { font-size:11px; color:#9ca3af; margin-top:1px; }

    /* ── Field ── */
    .field-group { margin-bottom:16px; }
    .field-group:last-child { margin-bottom:0; }
    .field-label {
        display:block; font-size:12px; font-weight:600;
        color:#374151; margin-bottom:5px;
    }
    .field-input {
        width:100%; padding:8px 11px; border-radius:8px;
        border:1px solid #e5e7eb; font-size:13px; color:#111827;
        background:#f9fafb; transition:border .15s, box-shadow .15s; outline:none;
    }
    .field-input:focus {
        border-color:#10b981; background:#fff;
        box-shadow:0 0 0 3px rgba(16,185,129,.1);
    }
    .field-hint { font-size:11px; color:#9ca3af; margin-top:4px; }

    textarea.field-input { resize:vertical; min-height:80px; }

    /* ── Status pill ── */
    .status-pill {
        display:inline-flex; align-items:center; gap:5px;
        padding:3px 10px; border-radius:99px;
        font-size:11px; font-weight:600;
    }
    .status-pill.on  { background:#f0fdf7; color:#059669; }
    .status-pill.off { background:#fef2f2; color:#dc2626; }
    .status-dot { width:6px; height:6px; border-radius:50%; }

    /* ── Toggle eye button ── */
    .eye-btn {
        position:absolute; right:10px; top:50%; transform:translateY(-50%);
        width:28px; height:28px; border:none; background:transparent;
        color:#9ca3af; cursor:pointer; display:flex; align-items:center;
        justify-content:center; border-radius:6px; transition:color .15s, background .15s;
    }
    .eye-btn:hover { color:#374151; background:#f3f4f6; }

    /* ── Save button ── */
    .btn-save {
        display:inline-flex; align-items:center; gap:7px;
        padding:10px 28px; border-radius:10px;
        background:#10b981; color:#fff;
        font-size:13px; font-weight:700;
        border:none; cursor:pointer;
        transition:background .15s, box-shadow .15s;
        box-shadow:0 4px 14px rgba(16,185,129,.2);
    }
    .btn-save:hover { background:#059669; box-shadow:0 4px 18px rgba(16,185,129,.3); }

    /* ── Test WA button ── */
    .btn-test {
        display:inline-flex; align-items:center; gap:6px;
        padding:7px 14px; border-radius:8px;
        background:#eff6ff; color:#2563eb;
        font-size:12px; font-weight:600;
        border:none; cursor:pointer;
        transition:background .15s;
        text-decoration:none;
    }
    .btn-test:hover { background:#dbeafe; }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-[17px] font-bold text-gray-800 leading-tight">Pengaturan Sistem</h1>
        <p class="text-[12px] text-gray-400 mt-0.5">Konfigurasi gym, pembayaran, dan integrasi WhatsApp</p>
    </div>
</div>

<form action="{{ route('settings.update') }}" method="POST" id="settings-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- ══ IDENTITAS BISNIS ══ --}}
        <div class="card">
            <div class="card-section-header">
                <div class="card-section-icon bg-emerald-50">
                    <i class="fa-solid fa-building text-emerald-500"></i>
                </div>
                <div>
                    <div class="card-section-title">Identitas Bisnis</div>
                    <div class="card-section-sub">Informasi dasar gym Anda</div>
                </div>
            </div>
            <div class="p-5">

                <div class="field-group">
                    <label class="field-label">Nama Gym</label>
                    <input type="text" name="nama_gym"
                           value="{{ \App\Models\Setting::getValue('nama_gym') }}"
                           class="field-input"
                           placeholder="cth. Ahmad Fitness Center">
                </div>

                <div class="field-group">
                    <label class="field-label">Alamat
                        <span class="text-gray-400 font-normal">(tampil di struk)</span>
                    </label>
                    <textarea name="alamat_gym" rows="3" class="field-input"
                              placeholder="Alamat lengkap gym...">{{ \App\Models\Setting::getValue('alamat_gym') }}</textarea>
                </div>

                <div class="field-group">
                    <label class="field-label">Nomor Telepon Bisnis</label>
                    <input type="text" name="no_telp"
                           value="{{ \App\Models\Setting::getValue('no_telp') }}"
                           class="field-input" placeholder="0812xxxx">
                </div>

                <div class="field-group">
                    <label class="field-label">Link Google Maps
                        <span class="text-gray-400 font-normal">(iframe src)</span>
                    </label>
                    <input type="text" name="google_maps_url"
                           value="{{ \App\Models\Setting::getValue('google_maps_url') }}"
                           class="field-input"
                           placeholder="https://www.google.com/maps/embed?pb=...">
                    <div class="field-hint">Paste URL dari tombol "Embed a map" di Google Maps.</div>
                </div>

                <div class="field-group">
                    <label class="field-label">Jam Operasional</label>
                    <textarea name="jam_operasional" rows="3" class="field-input"
                              placeholder="cth. Senin–Jumat 06.00–22.00&#10;Sabtu–Minggu 07.00–20.00">{{ \App\Models\Setting::getValue('jam_operasional') }}</textarea>
                </div>

                <div class="field-group">
                    <label class="field-label">Instagram</label>
                    <div class="relative">
                        <span class="absolute left-10 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 font-medium">instagram.com/</span>
                        <i class="fa-brands fa-instagram absolute left-3 top-1/2 -translate-y-1/2 text-pink-400 text-[13px]"></i>
                        <input type="text" name="instagram"
                               value="{{ \App\Models\Setting::getValue('instagram') }}"
                               class="field-input"
                               style="padding-left:130px;"
                               placeholder="username">
                    </div>
                </div>

            </div>
        </div>

        {{-- ── RIGHT COLUMN ── --}}
        <div class="space-y-5">

            {{-- ══ INFORMASI PEMBAYARAN ══ --}}
            <div class="card">
                <div class="card-section-header">
                    <div class="card-section-icon bg-blue-50">
                        <i class="fa-solid fa-credit-card text-blue-500"></i>
                    </div>
                    <div>
                        <div class="card-section-title">Informasi Pembayaran</div>
                        <div class="card-section-sub">Data rekening untuk transfer online</div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="field-group mb-0">
                            <label class="field-label">Nama Bank & Atas Nama</label>
                            <input type="text" name="payment_bank"
                                   value="{{ \App\Models\Setting::getValue('payment_bank') }}"
                                   class="field-input"
                                   placeholder="BCA a/n Ahmad Gym">
                        </div>
                        <div class="field-group mb-0">
                            <label class="field-label">Nomor Rekening</label>
                            <input type="text" name="payment_rekening"
                                   value="{{ \App\Models\Setting::getValue('payment_rekening') }}"
                                   class="field-input"
                                   placeholder="1234567890">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ INTEGRASI WHATSAPP ══ --}}
            <div class="card">
                <div class="card-section-header">
                    <div class="card-section-icon bg-green-50">
                        <i class="fa-brands fa-whatsapp text-green-500"></i>
                    </div>
                    <div>
                        <div class="card-section-title">Integrasi WhatsApp API</div>
                        <div class="card-section-sub">Konfigurasi gateway WA otomatis</div>
                    </div>
                </div>
                <div class="p-5">

                    <div class="field-group">
                        <label class="field-label">API Gateway URL</label>
                        <input type="text" name="wa_api_url"
                               value="{{ \App\Models\Setting::getValue('wa_api_url') }}"
                               class="field-input"
                               placeholder="https://api.fonnte.com/send">
                    </div>

                    <div class="field-group">
                        <label class="field-label">API Token / Key</label>
                        <div class="relative">
                            <input id="wa-key-input"
                                   type="password" name="wa_api_key"
                                   value="{{ \App\Models\Setting::getValue('wa_api_key') }}"
                                   class="field-input pr-10"
                                   placeholder="Token rahasia Anda">
                            <button type="button" class="eye-btn" id="eye-toggle"
                                    onclick="toggleKey()">
                                <i class="fa-solid fa-eye text-[12px]" id="eye-icon"></i>
                            </button>
                        </div>
                        <div class="field-hint">
                            <i class="fa-solid fa-shield-halved text-amber-400 text-[10px] mr-1"></i>
                            Jangan bagikan token ini kepada siapapun.
                        </div>
                    </div>

                    {{-- Status + Test ── --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="text-[12px] font-medium text-gray-500">Status API:</span>
                            @if(\App\Models\Setting::getValue('wa_api_key'))
                                <span class="status-pill on">
                                    <span class="status-dot bg-emerald-500"></span> Terpasang
                                </span>
                            @else
                                <span class="status-pill off">
                                    <span class="status-dot bg-red-500"></span> Belum Setting
                                </span>
                            @endif
                        </div>
                        <a href="/tes-wa" target="_blank" class="btn-test">
                            <i class="fa-solid fa-paper-plane text-[11px]"></i> Tes Koneksi WA
                        </a>
                    </div>

                </div>
            </div>

        </div>{{-- end right col --}}
    </div>

    {{-- ── Save Bar ── --}}
    <div class="mt-5 flex items-center justify-end gap-3 bg-white rounded-xl border border-gray-100 px-5 py-4">
        <p class="text-[12px] text-gray-400 flex-1 hidden sm:block">
            <i class="fa-solid fa-circle-info text-blue-400 mr-1"></i>
            Perubahan akan langsung diterapkan setelah disimpan.
        </p>
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-floppy-disk text-[12px]"></i>
            Simpan Semua Perubahan
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
function toggleKey() {
    const inp  = document.getElementById('wa-key-input');
    const icon = document.getElementById('eye-icon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush