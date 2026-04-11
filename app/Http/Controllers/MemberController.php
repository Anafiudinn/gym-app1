<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaksi;
use App\Models\Membership;

class MemberController extends Controller
{
    // 🔹 LIST MEMBER
    public function index()
    {
        // Filter: Hanya tampilkan member yang sudah melewati proses aktivasi pertama kali
        // (Artinya mereka sudah punya record di tabel memberships)
        $members = Member::has('membership')
            ->latest()
            ->paginate(10); // Pakai paginate biar rapi kalau data banyak

        return view('member.index', compact('members'));
    }

    // 🔹 DETAIL MEMBER
    public function show($id)
    {
        $member = Member::findOrFail($id);

        $transaksi = Transaksi::where('member_id', $id)->latest()->get();
        $membership = Membership::where('member_id', $id)->latest()->get();

        return view('member.show', compact('member', 'transaksi', 'membership'));
    }

    // 🔹 NONAKTIFKAN / AKTIFKAN
    public function toggleStatus($id)
    {
        $member = Member::findOrFail($id);

        $member->status = $member->status == 'aktif' ? 'nonaktif' : 'aktif';
        $member->save();

        return back()->with('success', 'Status member diperbarui');
    }
}
