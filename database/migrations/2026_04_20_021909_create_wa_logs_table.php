<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wa_logs', function (Blueprint $table) {
            $table->id();
            $table->string('target'); // Nomor HP tujuan
            $table->text('message'); // Isi pesan WA
            $table->string('status'); // 'success' atau 'failed'
            $table->text('response')->nullable(); // JSON balasan dari Fonnte (buat bukti)
            $table->timestamps(); // Bakal otomatis ada created_at (waktu kirim)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_logs');
    }
};
