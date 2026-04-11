<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Membership;
use App\Models\Paket;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    // 📌 tampil data transaksi
    public function index()
    {
        $data = Transaksi::with(['member', 'paket'])->latest()->get();
        $paket = Paket::all();
        $members = Member::all();

        return view('transaksi.index', compact('data', 'paket', 'members'));
    }

    // ==============================
    // 🔹 1. TRANSAKSI HARIAN (TAMU)
    // ==============================
    public function storeHarian(Request $request)
    {

        $request->validate([
            'nama_tamu' => 'required'
        ]);

        // ambil paket harian
        $paket = Paket::where('nama_paket', 'Harian')->first();

        Transaksi::create([
            'kode_invoice' => 'INV-' . strtoupper(Str::random(6)),
            'nama_tamu' => $request->nama_tamu,
            'paket_id' => $paket->id,
            'tipe' => 'harian',
            'jumlah_bayar' => $paket->harga,
            'metode_pembayaran' => 'cash',
            'status' => 'dibayar',
            'tanggal_pembayaran' => now()
        ]);

        return back()->with('success', 'Transaksi harian berhasil');
    }

    // ==================================
    // 🔹 2. DAFTAR / PERPANJANG MEMBER
    // ==================================
    public function storeMembership(Request $request)
    {
        $paket = Paket::findOrFail($request->paket_id);

        // =========================
        // 🔹 MEMBER BARU
        // =========================
        if ($request->tipe_member == 'baru') {

            $member = Member::create([
                'kode_member' => 'GYM-' . rand(1000, 9999),
                'nama' => $request->nama,
                'no_wa' => $request->no_wa,
                'jenis_kelamin' => $request->jenis_kelamin,
                'status' => 'aktif',
                'tanggal_daftar' => now()
            ]);
        } else {
            // =========================
            // 🔹 PERPANJANGAN
            // =========================
            $member = Member::findOrFail($request->member_id);
        }

        // hitung masa aktif
        $start = now();

        // kalau masih aktif → lanjut dari expired
        if ($member->tanggal_kadaluarsa && now()->lt($member->tanggal_kadaluarsa)) {
            $start = $member->tanggal_kadaluarsa;
        }

        $end = \Carbon\Carbon::parse($start)->addDays($paket->durasi_hari);

        // transaksi
        $transaksi = Transaksi::create([
            'kode_invoice' => 'INV-' . strtoupper(Str::random(6)),
            'member_id' => $member->id,
            'paket_id' => $paket->id,
            'tipe' => 'membership',
            'jumlah_bayar' => $paket->harga,
            'metode_pembayaran' => 'cash',
            'status' => 'dibayar',
            'tanggal_pembayaran' => now()
        ]);

        // membership
        Membership::create([
            'member_id' => $member->id,
            'transaksi_id' => $transaksi->id,
            'paket_id' => $paket->id,
            'tanggal_mulai' => $start,
            'tanggal_selesai' => $end,
            'status' => 'aktif'
        ]);

        // update member
        $member->update([
            'status' => 'aktif',
            'tanggal_kadaluarsa' => $end
        ]);

        return back()->with('success', 'Membership berhasil diproses');
    }
}
