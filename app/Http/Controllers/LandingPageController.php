<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Member;
use App\Models\Paket;
use App\Models\Transaksi;
use App\Models\VerifikasiPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    // 🔹 HOME
    public function index()
    {
        $paket = Paket::all();

        // Hitung keramaian: Absensi valid hari ini + Transaksi harian hari ini
        $jumlahAbsen = Absensi::whereDate('waktu_masuk', now())->where('status', 'valid')->count();
        $jumlahHarian = Transaksi::whereDate('created_at', now())->where('tipe', 'harian')->count();
        $totalKeramaian = $jumlahAbsen + $jumlahHarian;

        // Logika sederhana status keramaian
        $statusKeramaian = 'Sepi';
        if ($totalKeramaian > 20) $statusKeramaian = 'Ramai';
        if ($totalKeramaian > 50) $statusKeramaian = 'Sangat Penuh';

        return view('landing.index', compact('paket', 'totalKeramaian', 'statusKeramaian'));
    }

    // 🔹 FORM DAFTAR
    public function create(Request $request)
    {
        // Mengambil semua paket KECUALI yang namanya 'Harian'
        $paket = Paket::where('nama_paket', '!=', 'Harian')->get();
        $selectedPaketId = $request->query('paket_id');

        return view('landing.daftar', compact('paket', 'selectedPaketId')); // Tambahkan ini
    }

    // 🔹 SIMPAN PENDAFTARAN
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_wa' => 'required',
            'jenis_kelamin' => 'required',
            'paket_id' => 'required|exists:pakets,id'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Cari apakah member sudah pernah terdaftar berdasarkan nomor WA
                $member = Member::where('no_wa', $request->no_wa)->first();

                if ($member) {
                    // PROTEKSI: Jika status member masih AKTIF, cegah pendaftaran ulang
                    if ($member->status === 'aktif') {
                        return redirect()->back()
                            ->withInput() // Agar data di form tidak hilang
                            ->with('error', 'Pendaftaran Gagal: Member dengan nomor ' . $request->no_wa . ' saat ini masih berstatus AKTIF.');
                    }

                    // Jika sudah ada tapi NONAKTIF (pendaftaran lama), update namanya
                    $member->update(['nama' => $request->nama]);
                } else {
                    // 2. Jika benar-benar member baru, buat datanya
                    $member = Member::create([
                        'no_wa' => $request->no_wa,
                        'nama' => $request->nama,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'kode_member' => 'GYM-' . rand(10000, 99999),
                        'status' => 'nonaktif', // Default nonaktif sampai pembayaran diverifikasi
                        'tanggal_daftar' => now()
                    ]);
                }

                $paket = Paket::findOrFail($request->paket_id);
                $waktuExpired = now()->addMinutes(30);

                // 3. Cek apakah ada transaksi pending yang belum expired
                $transaksi = Transaksi::where('member_id', $member->id)
                    ->where('status', 'pending')
                    ->latest()
                    ->first();

                if (!$transaksi) {
                    $transaksi = Transaksi::create([
                        'kode_invoice' => 'INV-' . strtoupper(Str::random(8)),
                        'member_id' => $member->id,
                        'paket_id' => $paket->id,
                        'tipe' => 'membership',
                        'channel' => 'online',
                        'jumlah_bayar' => $paket->harga,
                        'metode_pembayaran' => 'transfer',
                        'status' => 'pending',
                        'expired_at' => $waktuExpired
                    ]);
                } else {
                    // Update transaksi yang ada (misal dia ganti paket saat mau bayar)
                    $transaksi->update([
                        'paket_id' => $paket->id,
                        'jumlah_bayar' => $paket->harga,
                        'expired_at' => $waktuExpired
                    ]);
                }

                return redirect('/pembayaran/' . $transaksi->kode_invoice);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
    // 🔹 FORM UPLOAD

    public function pembayaran($kode)
    {
        $transaksi = Transaksi::where('kode_invoice', $kode)
            ->with(['member', 'paket', 'verifikasi'])
            ->firstOrFail();

        // Logika Auto-Reject
        if ($transaksi->status == 'pending' && $transaksi->expired_at && now()->gt($transaksi->expired_at)) {
            $transaksi->update(['status' => 'ditolak']);
        }

        // Tambahkan ini agar Blade tidak bingung
        $sisaDetik = $transaksi->expired_at ? now()->diffInSeconds($transaksi->expired_at, false) : 0;
        $masihAktif = $sisaDetik > 0 && $transaksi->status == 'pending';

        return view('landing.pembayaran', compact('transaksi', 'sisaDetik', 'masihAktif'));
    }
    // 🔹 PROSES SIMPAN BUKTI TRANSFER (POST)
    public function uploadBukti(Request $request, $kode)
    {
        $transaksi = Transaksi::where('kode_invoice', $kode)->firstOrFail();

        // 1. Cek apakah sudah dibayar
        if ($transaksi->status == 'dibayar') {
            return redirect()->back()->with('error', 'Pembayaran ini sudah tervalidasi.');
        }

        // 2. Cek apakah sudah expired
        if ($transaksi->expired_at && now()->gt($transaksi->expired_at)) {
            $transaksi->update(['status' => 'ditolak']); // Pastikan statusnya update
            return redirect()->back()->with('error', 'Maaf, waktu pembayaran sudah habis. Silahkan daftar ulang.');
        }

        $request->validate([
            'nama_rekening' => 'required|string|max:255',
            'nama_bank' => 'required|string|max:100',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $file = $request->file('bukti')->store('bukti', 'public');

        VerifikasiPembayaran::updateOrCreate(
            ['transaksi_id' => $transaksi->id],
            [
                'nama_rekening' => $request->nama_rekening,
                'nama_bank' => $request->nama_bank,
                'bukti_pembayaran' => $file,
                'status' => 'pending',
                'catatan_admin' => null
            ]
        );

        $transaksi->update(['status' => 'pending']);

        return redirect('/pembayaran/' . $kode)->with('success', 'Bukti berhasil dikirim!');
    }
    public function batal($kode)
    {
        $transaksi = Transaksi::where('kode_invoice', $kode)->firstOrFail();

        // Opsi A: Hapus data (Jika tidak ingin menumpuk sampah)
        // $transaksi->member()->delete();
        // $transaksi->delete();

        // Opsi B: Ubah status jadi Batal (Lebih aman untuk audit)
        $transaksi->update(['status' => 'batal']);

        return redirect('/')->with('success', 'Pendaftaran berhasil dibatalkan.');
    }
    public function konfirmasi($kode)
    {
        $transaksi = Transaksi::where('kode_invoice', $kode)
            ->with(['member', 'paket', 'verifikasi'])
            ->firstOrFail();

        return view('landing.konfirmasi', compact('transaksi'));
    }
    // Fitur Cek Status (Layanan)
    public function cekStatus(Request $request)
    {
        if ($request->has('search')) {
            $search = $request->search;

            $transaksi = Transaksi::where('kode_invoice', $search)
                ->orWhereHas('member', function ($q) use ($search) {
                    $q->where('no_wa', $search);
                })
                ->with(['member', 'paket', 'verifikasi'])
                ->latest()
                ->first();

            if (!$transaksi) {
                return back()->with('error', 'Data tidak ditemukan. Silahkan daftar terlebih dahulu.');
            }

            // REDIRECT OTOMATIS: Biar user nggak klik dua kali
            // Semua status (Pending, Ditolak, Dibayar) lari ke halaman pembayaran yang dynamic
            return redirect('/pembayaran/' . $transaksi->kode_invoice);
        }

        return view('landing.cek_status');
    }

    public function cekMembership(Request $request)
    {
        $member = null;

        if ($request->has('search')) {
            $search = $request->search;

            $member = \App\Models\Member::where('kode_member', $search)
                ->orWhere('no_wa', $search)
                ->with(['transaksi.paket'])
                ->first();

            if (!$member) {
                return back()->with('error', 'Member tidak ditemukan.');
            }
        }

        return view('landing.cek_membership', compact('member'));
    }
}
