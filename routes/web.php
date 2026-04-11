<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\VerifikasiPembayaranController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index']);
Route::get('/daftar', [LandingPageController::class, 'create']);
Route::post('/daftar', [LandingPageController::class, 'store']);
// 🔹 PUSAT PEMBAYARAN (SINGLE DYNAMIC PAGE)
// Halaman ini untuk menampilkan instruksi bayar & status (Lunas/Pending/Ditolak)
// PEMBAYARAN
Route::get('/pembayaran/{kode}', [LandingPageController::class, 'pembayaran'])->name('pembayaran');

Route::post('/pembayaran/{kode}/upload', [LandingPageController::class, 'uploadBukti'])->name('pembayaran.upload');

Route::get('/pembayaran/{kode}/batal', [LandingPageController::class, 'batal'])->name('pembayaran.batal');

// 🔹 FITUR LAINNYA
Route::post('/batal/{kode}', [LandingPageController::class, 'batal'])->name('pembayaran.batal');
Route::get('/konfirmasi/{kode}', [LandingPageController::class, 'konfirmasi']);
Route::get('/cek-status', [LandingPageController::class, 'cekStatus'])->name('pembayaran.cek');

// CEK MEMBERSHIP (Tambahkan method ini ke controller jika belum ada)
Route::get('/cek-membership', [LandingPageController::class, 'cekMembership'])->name('cek.membership');

// setelah login
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // member
    Route::get('/member', [MemberController::class, 'index'])->name('member.index');
    Route::get('/member/{id}', [MemberController::class, 'show']);
    Route::post('/member/{id}/toggle', [MemberController::class, 'toggleStatus']);

    // paket
    Route::get('/paket', [PaketController::class, 'index'])->name('paket.index');
    Route::post('/paket', [PaketController::class, 'store']);
    Route::put('/paket/{id}', [PaketController::class, 'update']);
    Route::delete('/paket/{id}', [PaketController::class, 'destroy']);

    // transaksi
    // halaman transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');

    // 🔹 transaksi harian
    Route::post('/transaksi/harian', [TransaksiController::class, 'storeHarian']);

    // 🔹 membership (daftar / perpanjang)
    Route::post('/transaksi/membership', [TransaksiController::class, 'storeMembership']);

    // verifikasi
    Route::get('/verifikasi', [VerifikasiPembayaranController::class, 'index'])->name('verifikasi.index');
    Route::post('/verifikasi/{id}/terima', [VerifikasiPembayaranController::class, 'terima']);
    Route::post('/verifikasi/{id}/tolak', [VerifikasiPembayaranController::class, 'tolak']);

    // absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [AbsensiController::class, 'store']);

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
