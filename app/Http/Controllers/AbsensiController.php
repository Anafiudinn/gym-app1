<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Default ke hari ini — otomatis reset tiap hari
        $date = $request->date ?? now()->format('Y-m-d');

        $query = Absensi::with('member')->latest();

        $query->whereDate('created_at', $date);

        if ($request->search) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('kode_member', 'like', '%' . $request->search . '%')
                    ->orWhere('no_wa', 'like', '%' . $request->search . '%'); // tambah no WA
            });
        }

        $absensi = $query->paginate(15);
        $totalHariIni = Absensi::whereDate('created_at', $date)->count();

        return view('absensi.index', compact('absensi', 'date', 'totalHariIni'));
    }

    public function cekMember(Request $request)
    {
        $keyword = $request->keyword;

        // Cari berdasarkan kode / nama / no WA
        $member = Member::where('kode_member', $keyword)
            ->orWhere('nama', 'like', '%' . $keyword . '%')
            ->orWhere('no_wa', $keyword)
            ->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member tidak ditemukan']);
        }

        $is_expired = !$member->tanggal_kadaluarsa || now()->gt($member->tanggal_kadaluarsa);
        $is_nonaktif = $member->status !== 'aktif';

        return response()->json([
            'success' => true,
            'data' => [
                'id'         => $member->id,
                'nama'       => $member->nama,
                'kode'       => $member->kode_member,
                'status'     => $member->status,
                'expired_at' => $member->tanggal_kadaluarsa
                    ? $member->tanggal_kadaluarsa->format('d M Y')
                    : '-',
                'is_expired'  => $is_expired,
                'is_nonaktif' => $is_nonaktif,
                'can_absen'   => !$is_expired && !$is_nonaktif,
                'manage_url'  => route('member.show', $member->id), // ← tambah ini
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['member_id' => 'required|exists:members,id']);

        $member = Member::find($request->member_id);

        if ($member->status !== 'aktif') {
            return back()->with('error', 'Member tidak aktif');
        }

        if (!$member->tanggal_kadaluarsa || now()->gt($member->tanggal_kadaluarsa)) {
            return back()->with('error', 'Membership sudah kadaluarsa');
        }

        // Cegah double absen di hari yang sama
        $sudahAbsen = Absensi::where('member_id', $member->id)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($sudahAbsen) {
            return back()->with('error', $member->nama . ' sudah absen hari ini');
        }

        Absensi::create([
            'member_id'   => $member->id,
            'waktu_masuk' => now(),
            'status'      => 'valid',
        ]);

        return back()->with('success', 'Absensi ' . $member->nama . ' berhasil dicatat!');
    }
}
