<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    // 🔹 halaman absen
    public function index(Request $request)
    {
        $query = Absensi::with('member')->latest();

        // Filter Search (Nama atau Kode Member)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                    ->orWhere('kode_member', 'like', "%$search%");
            });
        }

        // Filter Tanggal
        if ($request->has('tanggal') && $request->tanggal != '') {
            $query->whereDate('waktu_masuk', $request->tanggal);
        }

        $absensi = $query->paginate(10)->withQueryString();

        return view('absensi.index', compact('absensi'));
    }
    // 🔹 proses absen
    public function store(Request $request)
    {
        $request->validate([
            'kode_member' => 'required'
        ]);

        $member = Member::where('kode_member', $request->kode_member)->first();

        // ❌ kalau tidak ditemukan
        if (!$member) {
            return back()->with('error', 'Member tidak ditemukan');
        }

        // cek status aktif
        if ($member->status != 'aktif') {
            return back()->with('error', 'Member tidak aktif');
        }

        // cek masa berlaku
        if (!$member->tanggal_kadaluarsa || now()->gt($member->tanggal_kadaluarsa)) {

            Absensi::create([
                'member_id' => $member->id,
                'waktu_masuk' => now(),
                'status' => 'kadaluarsa'
            ]);

            return back()->with('error', 'Membership sudah kadaluarsa');
        }

        // ✅ valid
        Absensi::create([
            'member_id' => $member->id,
            'waktu_masuk' => now(),
            'status' => 'valid'
        ]);

        return back()->with('success', 'Absensi berhasil');
    }
}
