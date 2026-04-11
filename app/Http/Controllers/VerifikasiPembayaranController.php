<?php

namespace App\Http\Controllers;

use App\Models\VerifikasiPembayaran;
use App\Models\Transaksi;
use App\Models\Membership;
use App\Models\Member;
use Illuminate\Http\Request;

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

        // aktifkan membership
        if ($transaksi->member_id && $transaksi->paket_id) {

            $member = Member::find($transaksi->member_id);
            $paket = $transaksi->paket;

            $start = now();

            // kalau masih aktif → lanjut
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

        return back()->with('success', 'Pembayaran diterima');
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