<?php

use App\Http\Controllers\{AbsensiController, DashboardController, LandingPageController, LaporanController, MemberController, PaketController, ProfileController, TransaksiController, VerifikasiPembayaranController};
use App\Http\Controllers\RiwayatTransaksiController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Tanpa Login)
|--------------------------------------------------------------------------
*/

Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'index')->name('landing');
    Route::get('/daftar', 'create')->name('daftar.index');
    Route::post('/daftar', 'store')->name('daftar.store');

    // Fitur Pembayaran & Cek Status
    Route::get('/pembayaran/{kode}', 'pembayaran')->name('pembayaran');
    Route::post('/pembayaran/{kode}/upload', 'uploadBukti')->name('pembayaran.upload');
    Route::post('/pembayaran/{kode}/batal', 'batal')->name('pembayaran.batal'); // Diubah ke POST agar aman
    Route::get('/cek-status', 'cekStatus')->name('pembayaran.cek');
    Route::get('/cek-membership', 'cekMembership')->name('cek.membership');
    Route::get('/konfirmasi/{kode}', 'konfirmasi')->name('pembayaran.konfirmasi');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pengelolaan Member
    Route::controller(MemberController::class)->prefix('member')->name('member.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::put('/{id}', 'update')->name('update');
        Route::patch('/{id}/toggle', 'toggleStatus')->name('toggle');
    });

    // Pengelolaan Paket
    Route::controller(PaketController::class)->prefix('paket')->name('paket.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Transaksi (Harian & Membership)
    Route::controller(TransaksiController::class)->prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/harian', 'storeHarian')->name('harian');
        Route::post('/membership', 'storeMembership')->name('membership');
    });

    // Verifikasi Pembayaran Online
    Route::controller(VerifikasiPembayaranController::class)->prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{id}/terima', 'terima')->name('terima');
        Route::post('/{id}/tolak', 'tolak')->name('tolak');
    });

    //Riwayat Transaksi
    Route::get('/riwayat', [RiwayatTransaksiController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/excel', [RiwayatTransaksiController::class, 'exportExcel'])->name('riwayat.excel');
    Route::get('/riwayat/pdf', [RiwayatTransaksiController::class, 'exportPdf'])->name('riwayat.pdf');

    // Absensi Member
    Route::controller(AbsensiController::class)->prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/cek', 'cekMember')->name('cek');
        Route::post('/', 'store')->name('store');
    });

    // Laporan & Export
    Route::controller(LaporanController::class)->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/export', 'export')->name('export');
    });

    // Profile Settings
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });
});

require __DIR__ . '/auth.php';

Route::get('/debug-link-storage', function () {
    Artisan::call('storage:link');
    return "Storage link created successfully!";
});
