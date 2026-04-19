<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Membership;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // 🔹 LIST MEMBER
    public function index(Request $request)
    {

        // 2. Mulai query
        // Gunakan with() jika di view kamu menampilkan data membership untuk menghindari N+1 query problem
        $query = Member::has('memberships');

        // 3. Fungsi Search (Nama atau Kode Member)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode_member', 'like', "%{$search}%");
            });
        }

        // 4. Fungsi Filter Status default: aktif
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 5. Eksekusi query
        $members = $query->latest()->paginate(10)->withQueryString();

        // 6. Return ke view dengan membawa data members dan stats
        return view('member.index', compact('members'));
    }

    // 🔹 DETAIL MEMBER
    public function show($id)
    {
        $member = Member::findOrFail($id);
        $transaksi = Transaksi::where('member_id', $id)->latest()->get();

        // Ambil data dari tabel absensi, bukan tabel membership
        // Sesuaikan 'Absensi' dengan nama model absensi milikmu
        $absensi = \App\Models\Absensi::where('member_id', $id)
            ->latest()
            ->take(5)
            ->get();

        // Kirim variabel $absensi ke view
        return view('member.show', compact('member', 'transaksi', 'absensi'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_wa' => 'required|string|max:20',
        ]);

        $member = Member::findOrFail($id);
        $member->update([
            'nama' => $request->nama,
            'no_wa' => $request->no_wa,
        ]);

        return back()->with('success', 'Data member berhasil diperbarui!');
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
