<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        // Tabel transaksi
    Schema::table('transaksis', function (Blueprint $table) {
        // member_id biasanya sudah constrained, jadi HAPUS saja dari sini
        // Kita fokus ke kolom filter & sorting
        $table->index(['channel', 'status', 'created_at']); 
    });

    // Tabel verifikasi
    Schema::table('verifikasi_pembayarans', function (Blueprint $table) {
        // transaksi_id dan diverifikasi_oleh SUDAH di-index otomatis oleh constrained()
        // Jadi kita hanya perlu tambahkan yang belum:
        $table->index('status');
    });

    // Tabel members
    Schema::table('members', function (Blueprint $table) {
        $table->index('status');
        $table->index('tanggal_kadaluarsa');
    });

    // Tabel wa_logs
    Schema::table('wa_logs', function (Blueprint $table) {
        $table->index('status');
        $table->index('created_at'); // Tambahkan ini karena log sering dicari berdasarkan waktu
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex(['channel', 'status', 'created_at']);
        });

        Schema::table('verifikasi_pembayarans', function (Blueprint $table) {
            $table->dropIndex('status');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex('status');
            $table->dropIndex('tanggal_kadaluarsa');
        });

        Schema::table('wa_logs', function (Blueprint $table) {
            $table->dropIndex('status');
            $table->dropIndex('created_at');
        });
    }
};
