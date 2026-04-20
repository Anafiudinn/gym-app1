<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\Membership; // Tambahkan import ini
use App\Helpers\WhatsappHelper;
use Carbon\Carbon;

class MemberExpiredChecker extends Command
{
    protected $signature = 'member:check-expired';
    protected $description = 'Cek member yang akan expired dan nonaktifkan yang sudah lewat';

    public function handle()
    {
        $today = Carbon::today();

        // --- BAGIAN 1: NOTIFIKASI H-3 EXPIRED ---
        $reminderDate = Carbon::today()->addDays(3);
        $membersToRemind = Member::whereDate('tanggal_kadaluarsa', $reminderDate)
            ->where('status', 'aktif')
            ->get();

        foreach ($membersToRemind as $member) {
            $pesan = "Halo *{$member->nama}*,\n\nSekedar mengingatkan, masa aktif membership Anda di *Ahmad GYM* akan habis dalam 3 hari (Tanggal: " . Carbon::parse($member->tanggal_kadaluarsa)->format('d M Y') . ").\n\nYuk perpanjang sekarang agar tetap bisa latihan tanpa kendala! 💪";

            WhatsappHelper::send($member->no_wa, $pesan);
            $this->info("Reminder H-3 dikirim ke: " . $member->nama);
        }

        // --- BAGIAN 2: PENONAKTIFAN OTOMATIS ---
        $expiredMembers = Member::whereDate('tanggal_kadaluarsa', '<', $today)
            ->where('status', 'aktif')
            ->get();

        foreach ($expiredMembers as $member) {
            // 1. Update status di tabel Members (pakai 'expired' sesuai Enum)
            $member->update(['status' => 'expired']);

            // 2. Update status di tabel Memberships (pakai 'kadaluarsa' sesuai Enum migrasi membership)
            Membership::where('member_id', $member->id)
                ->where('status', 'aktif')
                ->update(['status' => 'kadaluarsa']);

            $pesanOff = "Halo *{$member->nama}*,\n\nMasa aktif membership Anda telah **HABIS**. Status membership telah otomatis menjadi EXPIRED.\n\nSilakan lakukan perpanjangan di kasir atau via website Ahmad GYM. Terima kasih!";

            WhatsappHelper::send($member->no_wa, $pesanOff);
            $this->info("Status updated to EXPIRED for: " . $member->nama);
        }

        $this->info("Proses pengecekan selesai!");
    }
}