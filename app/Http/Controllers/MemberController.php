<?php

namespace App\Http\Controllers;

use App\Helpers\WhatsappHelper;
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
    // MemberController.php

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_wa' => 'required|string|min:10|max:15',
        ]);

        // Proses pembersihan nomor WA
        $nomor = $request->no_wa;

        // 1. Hapus karakter non-digit (spasi, strip, dll)
        $nomor = preg_replace('/[^0-9]/', '', $nomor);

        // 2. Ubah awalan 0 ke 62
        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        // 3. Jika user input mulai dari 8... (tanpa 0 atau 62), tambahkan 62
        if (str_starts_with($nomor, '8')) {
            $nomor = '62' . $nomor;
        }

        $member = Member::findOrFail($id);
        $member->update([
            'nama' => $request->nama,
            'no_wa' => $nomor, // Simpan nomor yang sudah rapi
        ]);

        $pesan = "Halo *{$member->nama}*, data profil Anda di Ahmad GYM telah diperbarui.";

        // Panggil pake nama yang bener gess!
        WhatsappHelper::send($member->no_wa, $pesan);

        return back()->with('success', 'Data diperbarui & WA terkirim!');
    }

    public function toggleStatus($id)
    {
        $member = Member::findOrFail($id);
        $member->status = $member->status == 'aktif' ? 'nonaktif' : 'aktif';
        $member->save();

        if ($member->status == 'aktif') {
            // Pesan jika member diaktifkan kembali
            $pesan = "🔥 *Waktunya Bakar Lemak Lagi!* 🔥\n\n" .
                "Halo *{$member->nama}*,\n" .
                "Akun membership Anda di *Ahmad GYM* telah aktif kembali. Yuk, balik ke gym dan lanjutin progres latihanmu hari ini!\n\n" .
                "Sampai ketemu di area latihan, ya! 💪";
        } else {
            // Pesan jika member dinonaktifkan (karena masalah/admin)
            $pesan = "📢 *Informasi Membership Ahmad GYM* 📢\n\n" .
                "Halo *{$member->nama}*,\n" .
                "Kami ingin menginformasikan bahwa saat ini akun membership Anda statusnya menjadi *NONAKTIF*.\n\n" .
                "Hal ini bisa disebabkan oleh beberapa alasan (masa berlaku habis, administrasi, atau kebijakan gym). Jika ada kendala atau ingin mengaktifkan kembali, silakan hubungi *Admin Ahmad GYM* di meja depan atau balas pesan ini.\n\n" .
                "Tetap sehat selalu!";
        }

        // Panggil helper yang bener namanya: WhatsappHelper
        \App\Helpers\WhatsappHelper::send($member->no_wa, $pesan);

        return back()->with('success', 'Status berhasil diubah & Notifikasi WA terkirim!');
    }
    // Tambahkan helper private function di bawah jika belum ada
    private function sendWhatsApp($target, $message)
    {
        // Gunakan logika pengiriman WA kamu (Fonnte/API Custom)
        // Contoh sederhana pakai file_get_contents atau curl
        // ...
    }
}
