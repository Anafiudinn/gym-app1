<?php

namespace App\Http\Controllers;

use App\Helpers\WhatsappHelper;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Transaksi;
use App\Models\VerifikasiPembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikasiPembayaranController extends Controller
{
    // 🔹 list verifikasi
    public function index()
    {
        $data = VerifikasiPembayaran::with('transaksi.member', 'transaksi.paket')
            ->latest()
            ->get();

        return view('verifikasi.index', compact('data'));
    }

    // 🔹 TERIMA
    public function terima($id)
    {
        $verif = VerifikasiPembayaran::findOrFail($id);
        // Load relasi member dan paket biar datanya siap dipakai
        $transaksi = $verif->transaksi->load('member', 'paket');

        return DB::transaction(function () use ($verif, $transaksi) {
            // 1. Update status verifikasi
            $verif->update([
                'status' => 'diterima',
                'diverifikasi_oleh' => auth()->id(),
                'tanggal_verifikasi' => now()
            ]);

            // 2. Update status transaksi
            $transaksi->update([
                'status' => 'dibayar',
                'tanggal_pembayaran' => now()
            ]);

            // --- MULAI SIAPKAN PESAN WA ---
            // Buat variabel awal di sini biar nggak "Undefined"
            $pesanWA = "Halo {$transaksi->member->nama},\n\nPembayaran Anda untuk paket *{$transaksi->paket->nama_paket}* telah diverifikasi dan **DITERIMA**. ✅";

            // Aktifkan membership (jika tipe membership)
            if ($transaksi->tipe === 'membership' && $transaksi->member_id) {
                $member = Member::lockForUpdate()->find($transaksi->member_id);
                $paket = $transaksi->paket;
                // Di fungsi terima()
                $start = now();
                if ($member->tanggal_kadaluarsa && Carbon::parse($member->tanggal_kadaluarsa)->isFuture()) {
                    $start = Carbon::parse($member->tanggal_kadaluarsa);
                }
                $end = Carbon::parse($start)->copy()->addDays($paket->durasi_hari); // Pastikan pakai copy()

                Membership::create([
                    'member_id' => $member->id,
                    'transaksi_id' => $transaksi->id,
                    'paket_id' => $paket->id,
                    'tanggal_mulai' => $start,
                    'tanggal_selesai' => $end,
                    'status' => 'aktif'
                ]);

                $member->update([
                    'status' => 'aktif',
                    'tanggal_kadaluarsa' => $end
                ]);

                // Tambahkan info kadaluarsa ke variabel pesan yang sudah ada
                $pesanWA .= "\n\nMembership Anda sekarang *AKTIF* hingga: *" . \Carbon\Carbon::parse($end)->format('d M Y') . "*.\nSilakan gunakan Member Card Anda untuk absensi kedtangan.";
            }

            $pesanWA .= "\n\nTerima kasih telah bergabung dengan Ahmad GYM! 💪";

            // 3. KIRIM WA (Gunakan no_wa)
            \App\Helpers\WhatsappHelper::send($transaksi->member->no_wa, $pesanWA);

            return back()->with('success', 'Pembayaran berhasil diverifikasi & Notifikasi terkirim!');
        });

    }
    // 🔹 TOLAK
    public function tolak(Request $request, $id)
    {
        $verif = VerifikasiPembayaran::findOrFail($id);

        $verif->update([
            'status' => 'ditolak',
            'catatan_admin' => $request->catatan_admin
        ]);

        $verif->transaksi->update([
            'status' => 'ditolak',
            'expired_at' => now()->addHours(24), // kasih waktu upload ulang 24 jam
        ]);

        // Tambah ini ↓
        session()->flash('notif_ditolak', true);

        // KIRIM WA NOTIFIKASI PENOLAKAN
        $alasan = $request->catatan_admin ?? 'Bukti transfer tidak sesuai atau tidak terbaca.';
        $pesanWA = "Halo {$verif->transaksi->member->nama},\n\nMohon maaf, pembayaran Anda dengan kode transaksi *{$verif->transaksi->kode_invoice}* telah **DITOLAK**. ❌";
        $pesanWA .= "\n\n*Alasan:* {$alasan}";
        $pesanWA .= "\n\nSilakan lakukan upload ulang bukti pembayaran yang benar melalui menu layanan cek pendaftaran input kode invoice atau nomer wa kamu, atau hubungi admin untuk bantuan lebih lanjut.";

        WhatsappHelper::send($verif->transaksi->member->no_wa, $pesanWA);

        return back()->with('success', 'Pembayaran ditolak & Notifikasi terkirim');
    }
}