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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_invoice')->unique();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('nama_tamu')->nullable();
            $table->foreignId('paket_id')->nullable()->constrained('paket')->nullOnDelete();
            $table->enum('tipe', ['harian', 'membership']);
            $table->integer('jumlah_bayar');
            $table->enum('metode_pembayaran', ['transfer', 'cash']);
            $table->enum('status', ['pending', 'dibayar', 'ditolak'])->default('pending');
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
