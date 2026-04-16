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
    // 🔹 SIMPAN PENDAFTARAN
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'no_wa' => 'required',
            'jenis_kelamin' => 'required',
            'paket_id' => 'required'
        ]);

          try {
            return DB::transaction(function () use ($request) {
                $paket = Paket::findOrFail($request->paket_id);
            

                // 2. LOGIKA SAKTI: Cek apakah ada transaksi pending sebelumnya

        // 1. Update atau Create Member
        $member = Member::updateOrCreate(
            ['no_wa' => $request->no_wa],
            [
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'kode_member' => 'GYM-' . rand(1000, 9999), // Hanya diisi jika baru
                'status' => 'nonaktif',
                'tanggal_daftar' => now()
            ]
        );

        $paket = Paket::findOrFail($request->paket_id);

        // 2. LOGIKA SAKTI: Cek apakah ada transaksi pending sebelumnya
        $transaksi = Transaksi::where('member_id', $member->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$transaksi) {
            // Jika tidak ada transaksi pending, baru buat invoice baru
            $transaksi = Transaksi::create([
                'kode_invoice' => 'INV-' . strtoupper(Str::random(6)),
                'member_id' => $member->id,
                'paket_id' => $paket->id,
                'tipe' => 'membership',
                'jumlah_bayar' => $paket->harga,
                'metode_pembayaran' => 'transfer',
                'status' => 'pending'
            ]);
        } else {
            // Jika ada, update paketnya (siapa tau user ganti pilihan paket)
            $transaksi->update([
                'paket_id' => $paket->id,
                'jumlah_bayar' => $paket->harga
            ]);
        }

        return redirect('/pembayaran/' . $transaksi->kode_invoice);
            });
        } catch (\Exception $e) {
            // Log error jika perlu
            return back()->with('error', 'Terjadi kesalahan saat memproses pendaftaran. Silakan coba lagi.');
        }

    }

    // 🔹 FORM UPLOAD

    public function pembayaran($kode)
    {
        $transaksi = Transaksi::where('kode_invoice', $kode)
            ->with(['member', 'paket', 'verifikasi'])
            ->firstOrFail();

        // Kalau statusnya lunas/dibayar, kita bisa kasih view khusus atau 
        // tetep di halaman ini tapi dengan tampilan "Lunas" (lebih disarankan)
        return view('landing.pembayaran', compact('transaksi'));
    }
    // 🔹 PROSES SIMPAN BUKTI TRANSFER (POST)
    public function uploadBukti(Request $request, $kode)
    {
        $transaksi = Transaksi::where('kode_invoice', $kode)->firstOrFail();

        // Proteksi: Jika sudah lunas, jangan boleh upload lagi
        if ($transaksi->status == 'dibayar') {
            return redirect()->back()->with('error', 'Pembayaran ini sudah tervalidasi.');
        }

        $request->validate([
            'nama_rekening' => 'required|string|max:255',
            'nama_bank' => 'required|string|max:100',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // 3. Simpan File ke Storage (Folder: public/bukti)
        $file = $request->file('bukti')->store('bukti', 'public');

        // 4. Gunakan updateOrCreate agar data tidak duplikat di tabel verifikasi
        // Ini berguna kalau user upload ulang karena ditolak sebelumnya
        \App\Models\VerifikasiPembayaran::updateOrCreate(
            ['transaksi_id' => $transaksi->id],
            [
                'nama_rekening' => $request->nama_rekening,
                'nama_bank' => $request->nama_bank,
                'bukti_pembayaran' => $file,
                'status' => 'pending', // Set balik ke pending buat dicek admin
                'catatan_admin' => null // Hapus catatan lama jika ada
            ]
        );

        // 5. Pastikan status transaksi tetap pending (biar admin tau ada update)
        $transaksi->update(['status' => 'pending']);

        // 6. Redirect balik ke halaman pembayaran (Single Dynamic Page tadi)
        return redirect('/pembayaran/' . $kode)->with('success', 'Bukti berhasil dikirim!, tunggu konfirmasi dari admin.');
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
