<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Membership;
use App\Models\Transaksi;
use App\Models\VerifikasiPembayaran;
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
    $transaksi = $verif->transaksi;

    // Gunakan Transaction agar data konsisten
    return DB::transaction(function () use ($verif, $transaksi) {
        // update verifikasi
        $verif->update([
            'status' => 'diterima',
            'diverifikasi_oleh' => auth()->id(),
            'tanggal_verifikasi' => now()
            
        ]);

        // update transaksi
        $transaksi->update([
            'status' => 'dibayar',
            'tanggal_pembayaran' => now()
        ]);

        // aktifkan membership (hanya jika tipe transaksi adalah membership)
        if ($transaksi->tipe === 'membership' && $transaksi->member_id) {
            $member = Member::lockForUpdate()->find($transaksi->member_id);
            $paket = $transaksi->paket;

            $start = now();
            if ($member->tanggal_kadaluarsa && now()->lt($member->tanggal_kadaluarsa)) {
                $start = $member->tanggal_kadaluarsa;
            }

            $end = \Carbon\Carbon::parse($start)->addDays($paket->durasi_hari);

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
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi!');
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
            'status' => 'ditolak'
        ]);

        return back()->with('success', 'Pembayaran ditolak');
    }
}