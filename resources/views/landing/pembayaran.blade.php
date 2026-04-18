@extends('layouts.landing')

@php
/*
|--------------------------------------------------------------------------
| State detection — sinkron dengan LandingPageController
|--------------------------------------------------------------------------
|
| Controller logic:
| - pembayaran() → auto-update status ke 'ditolak' jika expired_at lewat
| - uploadBukti() → cek expired sebelum proses upload
|
| STATE 1 → 'expired' : status == 'ditolak' DAN tidak ada verifikasi
| (auto-reject karena waktu habis, belum sempat bayar)
| STATE 2 → 'upload' : status == 'pending', belum ada verifikasi
| STATE 3 → 'menunggu' : sudah upload, verifikasi->status == 'pending'
| STATE 4 → 'ditolak' : sudah upload, verifikasi->status == 'ditolak'
| STATE 5 → 'sukses' : status == 'dibayar'
|
*/

$state = 'upload'; // default

// GANTI INI (sekitar baris 20-40)
if ($transaksi->status === 'dibayar') {
    $state = 'sukses';
} elseif ($transaksi->verifikasi) {
    // ✅ PRIORITAS VERIFIKASI
    if ($transaksi->verifikasi->status === 'pending') {
        $state = 'menunggu';
    } elseif ($transaksi->verifikasi->status === 'ditolak') {
        $state = 'ditolak'; // ← INI YANG FIX BUG
    }
} elseif ($transaksi->status === 'ditolak' || $transaksi->status === 'batal') {
    $state = 'expired';

} elseif ($transaksi->verifikasi) {
if ($transaksi->verifikasi->status === 'pending') {
$state = 'menunggu';
} elseif ($transaksi->verifikasi->status === 'ditolak') {
$state = 'ditolak';
}
}
// else: status == 'pending' tanpa verifikasi → 'upload' (default)

// ── Step indicator ──────────────────────────────────────────────────────
$step1 = 'done'; // Data Diri selalu done (sudah isi form)

$step2 = match($state) {
'sukses' => 'done',
'expired' => 'fail',
default => 'active',
};

$step3 = match($state) {
'sukses' => 'active',
default => '',
};

$line1 = 'active';
$line2 = $state === 'sukses' ? 'active' : '';

// ── Sisa waktu expired ──────────────────────────────────────────────────
$sisaDetik = null;
$masihAktif = false;
if ($transaksi->expired_at && $state === 'upload') {
$sisaDetik = now()->diffInSeconds($transaksi->expired_at, false);
$masihAktif = $sisaDetik > 0;
}
@endphp

@push('styles')
<style>
    /* =====================
       PAGE SHELL
    ===================== */
    .pem-page {
        min-height: calc(100svh - 60px);
        padding: 2rem 1rem 4rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .pem-header {
        width: 100%;
        max-width: 520px;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .pem-header h1 {
        font-size: clamp(1.5rem, 5vw, 2rem);
        font-weight: 800;
    }

    .pem-header h1 span {
        color: var(--lime);
    }

    .pem-header p {
        font-size: 0.85rem;
        color: var(--muted);
        margin-top: 0.25rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--muted);
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 500;
        width: 100%;
        max-width: 520px;
        margin-bottom: 1.25rem;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: var(--lime);
    }

    /* =====================
       STEP INDICATOR
    ===================== */
    .step-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 1.5rem;
        width: 100%;
        max-width: 520px;
    }

    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.35rem;
    }

    .step-num {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 0.82rem;
        border: 2px solid var(--border);
        background: var(--bg3);
        color: var(--muted);
        transition: all 0.3s;
        flex-shrink: 0;
    }

    .step-num.active {
        background: var(--lime);
        color: #000;
        border-color: var(--lime);
    }

    .step-num.done {
        background: rgba(170, 255, 0, 0.12);
        color: var(--lime);
        border-color: var(--lime);
    }

    /* NEW: state gagal/expired */
    .step-num.fail {
        background: rgba(255, 68, 68, 0.12);
        color: var(--danger, #ff4444);
        border-color: var(--danger, #ff4444);
    }

    .step-label {
        font-size: 0.65rem;
        color: var(--muted);
        white-space: nowrap;
        font-weight: 500;
    }

    .step-connector {
        height: 2px;
        flex: 1;
        background: var(--border);
        margin: 0 0.4rem;
        margin-bottom: 1.15rem;
        max-width: 60px;
        transition: background 0.3s;
    }

    .step-connector.active {
        background: var(--lime);
    }

    /* =====================
       MAIN CARD
    ===================== */
    .pem-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 20px;
        padding: 1.75rem;
        width: 100%;
        max-width: 520px;
        transition: border-color 0.3s;
    }

    /* =====================
       INVOICE SUMMARY BOX
    ===================== */
    .invoice-box {
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }

    .inv-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.7rem 1rem;
        font-size: 0.85rem;
        border-bottom: 1px solid var(--border);
    }

    .inv-row:last-child {
        border-bottom: none;
    }

    .inv-row .lbl {
        color: var(--muted);
    }

    .inv-row .val {
        font-weight: 600;
    }

    .inv-row .val.code {
        color: var(--lime);
        font-family: monospace;
        font-size: 0.82rem;
        letter-spacing: 0.5px;
    }

    .inv-row.total {
        background: rgba(170, 255, 0, 0.04);
    }

    .inv-row.total .val {
        color: var(--lime);
        font-weight: 800;
        font-size: 1rem;
    }

    /* =====================
       TRANSFER INFO BOX
    ===================== */
    .transfer-box {
        background: rgba(170, 255, 0, 0.05);
        border: 1.5px solid rgba(170, 255, 0, 0.18);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .transfer-box-left .t-lbl {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--lime);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.3rem;
    }

    .transfer-box-left .t-bank {
        font-weight: 800;
        font-size: 0.975rem;
    }

    .transfer-box-left .t-an {
        font-size: 0.8rem;
        color: var(--muted);
        margin-top: 0.1rem;
    }

    .copy-btn {
        background: rgba(170, 255, 0, 0.1);
        border: 1px solid rgba(170, 255, 0, 0.25);
        color: var(--lime);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Outfit', sans-serif;
        transition: 0.2s;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .copy-btn:hover {
        background: rgba(170, 255, 0, 0.18);
    }

    .copy-btn.copied {
        color: #000;
        background: var(--lime);
        border-color: var(--lime);
    }

    /* =====================
       COUNTDOWN TIMER
    ===================== */
    .countdown-box {
        background: rgba(245, 158, 11, 0.07);
        border: 1.5px solid rgba(245, 158, 11, 0.25);
        border-radius: 12px;
        padding: 0.85rem 1.1rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .countdown-box .cd-label {
        font-size: 0.78rem;
        color: var(--warning, #f59e0b);
        font-weight: 600;
    }

    .countdown-box .cd-timer {
        font-family: monospace;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--warning, #f59e0b);
        letter-spacing: 1px;
    }

    .countdown-box.danger {
        background: rgba(255, 68, 68, 0.07);
        border-color: rgba(255, 68, 68, 0.25);
    }

    .countdown-box.danger .cd-label,
    .countdown-box.danger .cd-timer {
        color: var(--danger, #ff4444);
    }

    /* =====================
       UPLOAD FORM
    ===================== */
    .field-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--muted);
        display: block;
        margin-bottom: 0.4rem;
    }

    .field-input {
        width: 100%;
        background: var(--bg3);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.8rem 1rem;
        color: var(--text);
        font-family: 'Outfit', sans-serif;
        font-size: 16px;
        outline: none;
        transition: border-color 0.2s;
        -webkit-appearance: none;
    }

    .field-input:focus {
        border-color: var(--lime);
    }

    .field-input::placeholder {
        color: #444;
    }

    .upload-zone {
        border: 2px dashed var(--border);
        border-radius: 14px;
        padding: 2rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.25s, background 0.25s;
        position: relative;
        background: var(--bg3);
    }

    .upload-zone:hover {
        border-color: #555;
    }

    .upload-zone.has-file,
    .upload-zone.drag {
        border-color: var(--lime);
        background: rgba(170, 255, 0, 0.04);
    }

    .upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .upload-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.06);
        display: grid;
        place-items: center;
        margin: 0 auto 0.75rem;
        font-size: 1.25rem;
    }

    .upload-zone p {
        font-size: 0.8rem;
        color: var(--muted);
    }

    .upload-zone .hint {
        font-size: 0.7rem;
        color: #555;
        margin-top: 0.25rem;
    }

    .upload-zone .file-name {
        font-size: 0.8rem;
        color: var(--lime);
        margin-top: 0.5rem;
        font-weight: 600;
    }

    .preview-img {
        width: 100%;
        max-height: 160px;
        object-fit: cover;
        border-radius: 10px;
        margin-top: 0.75rem;
        display: none;
    }

    /* =====================
       BUTTONS
    ===================== */
    .btn-submit {
        width: 100%;
        background: var(--lime);
        color: #000;
        border: none;
        padding: 0.9rem;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.925rem;
        cursor: pointer;
        font-family: 'Outfit', sans-serif;
        transition: background 0.2s, transform 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1rem;
        text-decoration: none;
    }

    .btn-submit:hover {
        background: var(--lime-dark);
    }

    .btn-submit:active {
        transform: scale(0.97);
    }

    .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-outline {
        display: block;
        width: 100%;
        text-align: center;
        background: var(--bg3);
        color: var(--text);
        border: 1.5px solid var(--border);
        padding: 0.8rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        transition: border-color 0.2s, color 0.2s;
        font-family: 'Outfit', sans-serif;
        cursor: pointer;
    }

    .btn-outline:hover {
        border-color: var(--lime);
        color: var(--lime);
    }

    .btn-danger-outline {
        display: block;
        width: 100%;
        text-align: center;
        background: transparent;
        color: #ff6666;
        border: 1.5px solid rgba(255, 102, 102, 0.3);
        padding: 0.8rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        transition: border-color 0.2s, background 0.2s;
        font-family: 'Outfit', sans-serif;
        cursor: pointer;
        margin-top: 0.75rem;
    }

    .btn-danger-outline:hover {
        border-color: #ff4444;
        background: rgba(255, 68, 68, 0.07);
    }

    .btn-danger-text {
        display: block;
        text-align: center;
        color: #ff6666;
        font-size: 0.78rem;
        text-decoration: none;
        margin-top: 0.875rem;
        opacity: 0.6;
        transition: opacity 0.2s;
        cursor: pointer;
        background: none;
        border: none;
        width: 100%;
        font-family: 'Outfit', sans-serif;
    }

    .btn-danger-text:hover {
        opacity: 1;
    }

    /* =====================
       STATE ICONS
    ===================== */
    .state-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        margin: 0 auto 1rem;
        font-size: 1.6rem;
    }

    .state-icon.menunggu {
        background: rgba(245, 158, 11, 0.1);
        border: 2px solid rgba(245, 158, 11, 0.3);
        animation: spin-slow 4s linear infinite;
    }

    .state-icon.sukses {
        background: rgba(170, 255, 0, 0.1);
        border: 2px solid rgba(170, 255, 0, 0.4);
    }

    .state-icon.ditolak {
        background: rgba(255, 68, 68, 0.1);
        border: 2px solid rgba(255, 68, 68, 0.3);
    }

    .state-icon.expired {
        background: rgba(255, 68, 68, 0.08);
        border: 2px solid rgba(255, 68, 68, 0.25);
    }

    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .state-title {
        font-size: 1.25rem;
        font-weight: 800;
        text-align: center;
        margin-bottom: 0.4rem;
    }

    .state-title.sukses {
        color: var(--lime);
    }

    .state-title.ditolak {
        color: var(--danger, #ff4444);
    }

    .state-title.expired {
        color: var(--danger, #ff4444);
    }

    .state-desc {
        font-size: 0.85rem;
        color: var(--muted);
        text-align: center;
        line-height: 1.6;
        margin-bottom: 1.25rem;
    }

    /* =====================
       MEMBER CARD (sukses)
    ===================== */
    /* Member Card */
    .member-card {
        background: linear-gradient(135deg, #1a2e0a 0%, #0f1f06 100%);
        border: 1.5px solid rgba(170, 255, 0, 0.3);
        border-radius: 16px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .member-card::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(170, 255, 0, 0.05);
    }

    .mc-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .mc-brand {
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--lime);
        letter-spacing: 1px;
    }

    .mc-status {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
    }

    .mc-status.aktif {
        background: rgba(170, 255, 0, 0.15);
        color: var(--lime);
    }

    .mc-status.nonaktif {
        background: rgba(255, 68, 68, 0.1);
        color: var(--danger);
    }

    .mc-icon {
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
        opacity: 0.3;
    }

    .mc-field {
        margin-bottom: 0.6rem;
    }

    .mc-label {
        font-size: 0.72rem;
        color: rgba(170, 255, 0, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .mc-value {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--text);
    }

    .mc-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(170, 255, 0, 0.1);
    }

    .mc-paket .mc-label {
        color: rgba(255, 255, 255, 0.4);
    }

    .mc-paket .mc-value {
        font-size: 0.9rem;
    }

    .mc-aktif-sd .mc-label {
        text-align: right;
        color: rgba(255, 255, 255, 0.4);
    }

    .mc-aktif-sd .mc-value {
        color: var(--lime);
        text-align: right;
    }


    /* =====================
       SECTION LABEL
    ===================== */
    .section-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* =====================
       DIVIDER
    ===================== */
    .divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1.25rem 0;
        color: var(--muted);
        font-size: 0.75rem;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* =====================
       SAVE CODE BOX
    ===================== */
    .save-code-box {
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem 1.1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .save-code-box .sc-hint {
        font-size: 0.72rem;
        color: var(--muted);
        margin-bottom: 0.2rem;
    }

    .save-code-box .sc-code {
        font-family: monospace;
        font-weight: 800;
        color: var(--lime);
        font-size: 1rem;
        letter-spacing: 1px;
    }

    /* =====================
       PROGRESS TIMELINE
    ===================== */
    .timeline {
        display: flex;
        flex-direction: column;
        gap: 0;
        margin-bottom: 1.5rem;
    }

    .tl-item {
        display: flex;
        gap: 0.875rem;
        align-items: flex-start;
    }

    .tl-dot-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-shrink: 0;
    }

    .tl-dot {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 0.75rem;
        border: 2px solid var(--border);
        background: var(--bg3);
        color: var(--muted);
        flex-shrink: 0;
    }

    .tl-dot.done {
        background: rgba(170, 255, 0, 0.12);
        border-color: var(--lime);
        color: var(--lime);
    }

    .tl-dot.active {
        background: rgba(245, 158, 11, 0.12);
        border-color: var(--warning, #f59e0b);
        color: var(--warning, #f59e0b);
    }

    .tl-dot.fail {
        background: rgba(255, 68, 68, 0.12);
        border-color: var(--danger, #ff4444);
        color: var(--danger, #ff4444);
    }

    .tl-line {
        width: 2px;
        height: 24px;
        background: var(--border);
        margin: 3px 0;
    }

    .tl-line.done {
        background: var(--lime);
    }

    .tl-content {
        padding: 0.1rem 0 1rem;
    }

    .tl-content .tl-title {
        font-size: 0.875rem;
        font-weight: 700;
    }

    .tl-content .tl-desc {
        font-size: 0.78rem;
        color: var(--muted);
        margin-top: 0.15rem;
    }

    /* =====================
       REJECT ALERT
    ===================== */
    .reject-alert {
        background: rgba(255, 68, 68, 0.07);
        border: 1.5px solid rgba(255, 68, 68, 0.2);
        border-radius: 12px;
        padding: 1rem 1.1rem;
        margin-bottom: 1.25rem;
    }

    .reject-alert .r-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--danger, #ff4444);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.3rem;
    }

    .reject-alert .r-note {
        font-size: 0.8rem;
        color: #ccc;
        line-height: 1.5;
    }

    /* =====================
       ALERT FLASH
    ===================== */
    .alert {
        padding: 0.8rem 1rem;
        border-radius: 10px;
        font-size: 0.85rem;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .alert-success {
        background: rgba(170, 255, 0, 0.08);
        border: 1px solid rgba(170, 255, 0, 0.25);
        color: var(--lime);
    }

    .alert-error {
        background: rgba(255, 68, 68, 0.08);
        border: 1px solid rgba(255, 68, 68, 0.25);
        color: #ff6666;
    }

    /* =====================
       EXPIRED INFO BOX
    ===================== */
    .expired-box {
        background: rgba(255, 68, 68, 0.06);
        border: 1.5px solid rgba(255, 68, 68, 0.2);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        text-align: center;
    }

    .expired-box .exp-time {
        font-family: monospace;
        font-size: 0.85rem;
        color: #ff6666;
        margin-top: 0.35rem;
    }

    /* =====================
       RESPONSIVE
    ===================== */
    @media (max-width: 480px) {
        .pem-card {
            padding: 1.35rem;
        }

        .transfer-box {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .copy-btn {
            align-self: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="pem-page">

        {{-- HEADER --}}
        <div class="pem-header">
            <h1>Pendaftaran <span>Member</span></h1>
            <p>{{\App\Models\Setting::getValue('nama_gym')}} 
            <br>Selesaikan pembayaran untuk mengaktifkan membership</p>
        </div>

        {{-- STEP INDICATOR --}}
        <div class="step-wrap">
            <div class="step-item">
                <div class="step-num {{ $step1 }}">
                    @if($step1 === 'done') ✓ @else 1 @endif
                </div>
                <span class="step-label">Data Diri</span>
            </div>

            <div class="step-connector {{ $line1 }}"></div>

            <div class="step-item">
                <div class="step-num {{ $step2 }}">
                    @if($step2 === 'done') ✓
                    @elseif($step2 === 'fail') ✕
                    @else 2
                    @endif
                </div>
                <span class="step-label">Pembayaran</span>
            </div>

            <div class="step-connector {{ $line2 }}"></div>

            <div class="step-item">
                <div class="step-num {{ $step3 }}">
                    @if($step3 === 'done') ✓ @else 3 @endif
                </div>
                <span class="step-label">Aktif</span>
            </div>
        </div>

        {{-- BACK LINK --}}
        @if($state === 'upload')
        <a href="/daftar" class="back-link">← Kembali ke form pendaftaran</a>
        @else
        <a href="/" class="back-link">← Kembali ke beranda</a>
        @endif

        {{-- MAIN CARD --}}
        <div class="pem-card">

            {{-- ============================================================
                 STATE: EXPIRED — waktu pembayaran habis, belum sempat upload
            ============================================================ --}}
            @if($state === 'expired')

            <div class="state-icon expired">⏰</div>
            <div class="state-title expired">Waktu Habis</div>
            <div class="state-desc">
                Batas waktu pembayaran untuk invoice ini sudah lewat.<br>
                Silahkan daftar ulang untuk membuat invoice baru.
            </div>

            <div class="expired-box">
                <div style="font-size:0.78rem;color:var(--muted);">Invoice yang kedaluwarsa</div>
                <div style="font-family:monospace;font-weight:800;color:#ff6666;font-size:1rem;margin-top:0.3rem;">
                    {{ $transaksi->kode_invoice }}
                </div>
                @if($transaksi->expired_at)
                <div class="exp-time">
                    Berakhir pada {{ \Carbon\Carbon::parse($transaksi->expired_at)->format('d M Y, H:i') }} WIB
                </div>
                @endif
            </div>

            <div class="invoice-box">
                <div class="inv-row">
                    <span class="lbl">Nama</span>
                    <span class="val">{{ $transaksi->member->nama }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">Paket</span>
                    <span class="val">{{ $transaksi->paket->nama_paket }}</span>
                </div>
                <div class="inv-row total">
                    <span class="lbl">Total</span>
                    <span class="val">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
                </div>
            </div>

            <a href="/daftar?no_wa={{ $transaksi->member->no_wa }}" class="btn-submit" style="text-decoration:none;">
                Daftar Ulang
            </a>
            <a href="/" class="btn-outline" style="margin-top:0.75rem;text-decoration:none;">
                Kembali ke Beranda
            </a>

            {{-- ============================================================
      STATE: SUKSES — membership aktif
============================================================ --}}
            @elseif($state === 'sukses')

            <div class="state-icon sukses">✓</div>
            <div class="state-title sukses">Membership Aktif!</div>
            <div class="state-desc">
                Selamat {{ $transaksi->member->nama }}! Kamu resmi jadi member JefryGym.<br>
                Tunjukkan kode member saat masuk gym.
            </div>

            @php
            // Mengambil data pendukung kartu dari objek $transaksi yang sudah ada
            $member = $transaksi->member;
            $tanggalAktif = $transaksi->member->tanggal_kadaluarsa
            ? \Carbon\Carbon::parse($transaksi->member->tanggal_kadaluarsa)
            : null;
            @endphp

            {{-- Member Card --}}
            <div class="member-card">
                <div class="mc-top">
                    <div class="mc-brand">JEFRYGYM</div>
                    <div class="mc-status {{ $member->status === 'aktif' ? 'aktif' : 'nonaktif' }}">
                        {{ ucfirst($member->status) }}
                    </div>
                </div>

                <div class="mc-icon">💳</div>

                <div class="mc-field">
                    <div class="mc-label">Member ID</div>
                    <div class="mc-value" style="color:var(--lime);">{{ $member->kode_member }}</div>
                </div>

                <div class="mc-field">
                    <div class="mc-label">Nama</div>
                    <div class="mc-value" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>{{ $member->nama }}</span>
                        <span style="font-size: 0.75rem; color: var(--muted); font-weight: 400;">
                            <i class="fab fa-whatsapp"></i> {{ $member->no_wa }}
                        </span>
                    </div>
                </div>

                <div class="mc-footer">
                    <div class="mc-paket">
                        <div class="mc-label">Paket</div>
                        <div class="mc-value">{{ $transaksi->paket->nama_paket }}</div>
                    </div>
                    @if($tanggalAktif)
                    <div class="mc-aktif-sd">
                        <div class="mc-label">Aktif s/d</div>
                        <div class="mc-value">{{ $tanggalAktif->format('d/m/Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>


            {{-- Detail transaksi --}}
            <div class="section-label">Detail Transaksi</div>
            <div class="invoice-box">
                <div class="inv-row">
                    <span class="lbl">Kode Invoice</span>
                    <span class="val code">{{ $transaksi->kode_invoice }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">Tanggal Verifikasi</span>
                    <span class="val">
                        {{ \Carbon\Carbon::parse($transaksi->updated_at)->format('d M Y') }}
                    </span>
                </div>
                <div class="inv-row">
                    <span class="lbl">Metode Bayar</span>
                    <span class="val">Transfer Bank</span>
                </div>
                @if($transaksi->verifikasi)
                <div class="inv-row">
                    <span class="lbl">Bank Pengirim</span>
                    <span class="val">{{ $transaksi->verifikasi->nama_bank }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">Nama Rekening</span>
                    <span class="val">{{ $transaksi->verifikasi->nama_rekening }}</span>
                </div>
                <div class="inv-row total">
                    <span class="lbl">Jumlah Bayar</span>
                    <span class="val">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>

            <a href="/" class="btn-submit" style="text-decoration:none;">
                Kembali ke Beranda
            </a>
            <a href="/cek-membership?search={{ $transaksi->member->no_wa }}"
                class="btn-outline"
                style="margin-top:0.75rem;text-decoration:none;">
                Lihat Status Membership
            </a>
            {{-- ============================================================
                 STATE: MENUNGGU — sudah upload, nunggu admin
            ============================================================ --}}
            @elseif($state === 'menunggu')

            <div class="state-icon menunggu">⏳</div>
            <div class="state-title">Menunggu Verifikasi</div>
            <div class="state-desc">
                Bukti pembayaran kamu sudah diterima dan sedang dicek admin.<br>
                Biasanya proses 1–24 jam kerja.
            </div>

            {{-- Simpan kode --}}
            <div class="save-code-box">
                <div class="sc-left">
                    <div class="sc-hint">Simpan kode ini untuk cek status</div>
                    <div class="sc-code">{{ $transaksi->kode_invoice }}</div>
                </div>
                <button class="copy-btn" onclick="copyText('{{ $transaksi->kode_invoice }}', this)">
                    Salin
                </button>
            </div>

            {{-- Progress timeline --}}
            <div class="section-label">Progress Pendaftaran</div>
            <div class="timeline">
                <div class="tl-item">
                    <div class="tl-dot-wrap">
                        <div class="tl-dot done">✓</div>
                        <div class="tl-line done"></div>
                    </div>
                    <div class="tl-content">
                        <div class="tl-title">Data Diri Tersimpan</div>
                        <div class="tl-desc">
                            {{ $transaksi->member->nama }} · {{ $transaksi->paket->nama_paket }}
                        </div>
                    </div>
                </div>

                <div class="tl-item">
                    <div class="tl-dot-wrap">
                        <div class="tl-dot done">✓</div>
                        <div class="tl-line"></div>
                    </div>
                    <div class="tl-content">
                        <div class="tl-title">Bukti Transfer Dikirim</div>
                        <div class="tl-desc">
                            {{ $transaksi->verifikasi->nama_bank }}
                            · {{ $transaksi->verifikasi->nama_rekening }}
                        </div>
                    </div>
                </div>

                <div class="tl-item">
                    <div class="tl-dot-wrap">
                        <div class="tl-dot active">●</div>
                    </div>
                    <div class="tl-content">
                        <div class="tl-title" style="color:var(--warning,#f59e0b);">
                            Menunggu Konfirmasi Admin
                        </div>
                        <div class="tl-desc">Proses verifikasi 1–24 jam kerja</div>
                    </div>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="section-label">Ringkasan Pembayaran</div>
            <div class="invoice-box">
                <div class="inv-row">
                    <span class="lbl">Nama</span>
                    <span class="val">{{ $transaksi->member->nama }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">No. WA</span>
                    <span class="val">{{ $transaksi->member->no_wa }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">Paket</span>
                    <span class="val">{{ $transaksi->paket->nama_paket }}</span>
                </div>
                <div class="inv-row total">
                    <span class="lbl">Total</span>
                    <span class="val">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
                </div>
            </div>

            <a href="/cek-status?search={{ $transaksi->kode_invoice }}"
                class="btn-outline"
                style="text-decoration:none;">
                Cek Status Pendaftaran
            </a>

            {{-- ============================================================
                 STATE: DITOLAK — admin reject, form upload ulang
            ============================================================ --}}
            @elseif($state === 'ditolak')

            <div class="state-icon ditolak">✕</div>
            <div class="state-title ditolak">Bukti Ditolak</div>
            <div class="state-desc">
                Bukti pembayaran kamu ditolak oleh admin.<br>
                Periksa catatan di bawah dan upload ulang.
            </div>

            {{-- Catatan penolakan admin --}}
            @if($transaksi->verifikasi && $transaksi->verifikasi->catatan_admin)
            <div class="reject-alert">
                <div class="r-title"><span>⚠</span> Catatan Admin</div>
                <div class="r-note">{{ $transaksi->verifikasi->catatan_admin }}</div>
            </div>
            @endif

            {{-- Info invoice --}}
            <div class="invoice-box">
                
                <div class="inv-row">
                    <span class="lbl">Nama</span>
                    <span class="val">{{ $transaksi->member->nama }}</span>
                </div>
           
                <div class="inv-row">
                    <span class="lbl">No. WA</span>
                    <span class="val">{{ $transaksi->member->no_wa }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">Invoice</span>
                    <span class="val code">{{ $transaksi->kode_invoice }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">Paket</span>
                    <span class="val">{{ $transaksi->paket->nama_paket }}</span>
                </div>
                <div class="inv-row total">
                    <span class="lbl">Total</span>
                    <span class="val">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Transfer info --}}
            <div class="transfer-box">
                <div class="transfer-box-left">
                    <div class="t-lbl">Transfer ke</div>
                    <div class="t-bank">{{\App\Models\Setting::getValue('payment_rekening')}}</div>
                    <div class="t-an">a/n {{\App\Models\Setting::getValue('payment_bank')}}</div>
                </div>
                <button class="copy-btn" onclick="copyText('1234567890', this)">Salin No.</button>
            </div>

            {{-- Form upload ulang --}}
            <div class="section-label">Upload Bukti Baru</div>
            @include('components.form_upload', [
            'kode' => $transaksi->kode_invoice,
            'verifikasi'=> $transaksi->verifikasi
            ])

            {{-- ============================================================
                 STATE: UPLOAD — belum upload sama sekali
            ============================================================ --}}
            @else

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            {{-- Countdown jika ada expired_at --}}
            @if($masihAktif && $sisaDetik !== null)
            <div class="countdown-box" id="countdownBox">
                <span class="cd-label">⏱ Batas waktu pembayaran</span>
                <span class="cd-timer" id="countdownTimer">--:--:--</span>
            </div>
            @endif

            {{-- Ringkasan pesanan --}}
            <div class="section-label">Ringkasan Pesanan</div>
            <div class="invoice-box">
                <div class="inv-row">
                    <span class="lbl">Kode Invoice</span>
                    <span class="val code">{{ $transaksi->kode_invoice }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">Nama</span>
                    <span class="val">{{ $transaksi->member->nama }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">No. WA</span>
                    <span class="val">{{ $transaksi->member->no_wa }}</span>
                </div>
                <div class="inv-row">
                    <span class="lbl">Paket</span>
                    <span class="val">{{ $transaksi->paket->nama_paket }}</span>
                </div>
                <div class="inv-row total">
                    <span class="lbl"><strong>Total</strong></span>
                    <span class="val">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Transfer info --}}
            <div class="transfer-box">
                <div class="transfer-box-left">
                    <div class="t-lbl">Transfer ke</div>
                    <div class="t-bank">{{\App\Models\Setting::getValue('payment_rekening')}}</div>
                    <div class="t-an">a/n {{\App\Models\Setting::getValue('payment_bank')}}</div>
                </div>
                <button class="copy-btn" onclick="copyText('1234567890', this)">Salin No.</button>
            </div>

            <div class="divider">lalu upload bukti di bawah ini</div>

            {{-- Form upload --}}
            @include('components.form_upload', [
            'kode' => $transaksi->kode_invoice,
            'verifikasi'=> null
            ])

            {{-- Tombol batal --}}
            <form action="/pembayaran/{{ $transaksi->kode_invoice }}/batal"
                method="POST"
                onsubmit="return confirm('Yakin ingin membatalkan pendaftaran ini?')">
                @csrf
                <button type="submit" class="btn-danger-text">
                    Batalkan Pendaftaran
                </button>
            </form>

            @endif

        </div>{{-- end .pem-card --}}
    </div>
</div>

<script>
    // ── Copy to clipboard ─────────────────────────────────────────────────
    function copyText(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.textContent;
            btn.textContent = '✓ Disalin';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.textContent = orig;
                btn.classList.remove('copied');
            }, 2000);
        }).catch(() => {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
        });
    }

 {{-- ── Countdown timer ── --}}
    @php
        $now = time(); // Ambil timestamp sekarang (detik)
        $expired = strtotime($transaksi->expired_at); // Ubah expired_at jadi detik
        
        $sisaDetik = $expired - $now; // Hitung selisihnya
        
        // Pastikan angka tidak negatif dan hanya tampil jika status pending
        if ($sisaDetik < 0 || $transaksi->status !== 'pending') {
            $sisaDetik = 0;
        }
        
        $masihAktif = $sisaDetik > 0;
    @endphp

    @if($masihAktif)
        (function() {
            // Gunakan Number() dan bungkus dengan petik untuk keamanan ekstra
            let sisa = Number("{{ $sisaDetik }}"); 

            const timerEl = document.getElementById('countdownTimer');
            const boxEl = document.getElementById('countdownBox');

            if (!timerEl) return;

            function format(s) {
                if (s <= 0) return "00:00:00";
                const h = String(Math.floor(s / 3600)).padStart(2, '0');
                const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
                const sc = String(s % 60).padStart(2, '0');
                return h + ":" + m + ":" + sc;
            }

            function tick() {
                if (sisa <= 0) {
                    timerEl.textContent = "00:00:00";
                    setTimeout(function() { location.reload(); }, 1000);
                    return;
                }

                timerEl.textContent = format(sisa);

                if (sisa <= 300 && boxEl) {
                    boxEl.classList.add('danger');
                }

                sisa--;
                setTimeout(tick, 1000);
            }

            tick();
        })();
    @else

          const el = document.getElementById('countdownTimer');
            if(el) el.textContent = "00:00:00";
       
    @endif
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    const previewImg = document.getElementById('previewImg');

    if (fileInput && uploadZone) {
        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (!file) return;

            if (fileName) fileName.textContent = '✓ ' + file.name;
            uploadZone.classList.add('has-file');

            if (previewImg && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        uploadZone.addEventListener('dragover', e => {
            e.preventDefault();
            uploadZone.classList.add('drag');
        });
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag'));
        uploadZone.addEventListener('drop', e => {
            e.preventDefault();
            uploadZone.classList.remove('drag');
            if (e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    }
</script>

@endsection