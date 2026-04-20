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
        if ($totalKeramaian > 20)
            $statusKeramaian = 'Ramai';
        if ($totalKeramaian > 50)
            $statusKeramaian = 'Sangat Penuh';

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
        // A. Bersihkan nomor WA dari karakter aneh (spasi, strip, plus) sebelum divalidasi
        $request->merge([
            'no_wa' => str_replace([' ', '-', '+'], '', $request->no_wa),
        ]);

        // B. Validasi Ketat
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_wa' => [
                'required',
                'numeric',
                'regex:/^(08|628)[0-9]{8,13}$/', // Awalan 08/628, total 10-15 digit
            ],
            'jenis_kelamin' => 'required|in:L,P',
            'paket_id' => 'required|exists:pakets,id'
        ], [
            'no_wa.regex' => 'Format nomor WhatsApp tidak valid (Gunakan format 08xxx atau 628xxx).',
            'no_wa.numeric' => 'Nomor WhatsApp harus berupa angka saja.',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $member = Member::where('no_wa', $request->no_wa)->first();

                if ($member) {
                    // Jika masih AKTIF, tendang balik
                    if ($member->status === 'aktif') {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Pendaftaran Gagal: Nomor ' . $request->no_wa . ' masih berstatus AKTIF, jika ingin melakukan perubahan data atau perpanjangan silahkan menghubungi admin.');
                    }
                    $member->update(['nama' => $request->nama]);
                } else {
                    $member = Member::create([
                        'no_wa' => $request->no_wa,
                        'nama' => $request->nama,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'kode_member' => 'GYM-' . rand(10000, 99999),
                        'status' => 'nonaktif',
                        'tanggal_daftar' => now()
                    ]);
                }

                $paket = Paket::findOrFail($request->paket_id);
                $waktuExpired = now()->addMinutes(30);

                // Cari transaksi pending yang benar-benar belum expired
                $transaksi = Transaksi::where('member_id', $member->id)
                    ->where('status', 'pending')
                    ->where('expired_at', '>', now()) // Tambahkan kondisi ini
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
                    // Update jika dia pilih paket berbeda saat pendaftaran ulang
                    $transaksi->update([
                        'paket_id' => $paket->id,
                        'jumlah_bayar' => $paket->harga,
                        'expired_at' => $waktuExpired
                    ]);
                }

                return redirect('/pembayaran/' . $transaksi->kode_invoice);
            });
        } catch (\Exception $e) {
            // Log error untuk debug internal
            \Log::error($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.')->withInput();
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
            $transaksi->update(['status' => 'ditolak']);
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

        // --- LOGIKA NOTIFIKASI ADMIN START ---
        try {
            $noAdmin = \App\Models\Setting::getValue('no_telp'); // Ambil nomor dari setting

            if ($noAdmin) {
                $namaMember = $transaksi->member->nama ?? 'Member Baru';
                $namaPaket = $transaksi->paket->nama_paket ?? 'Paket Gym';
                $total = number_format($transaksi->jumlah_bayar, 0, ',', '.');

                $pesanAdmin = "🔔 *ADA PEMBAYARAN MASUK!*\n\n";
                $pesanAdmin .= "Halo Admin Ahmad GYM, ada user yang baru saja upload bukti pembayaran:\n\n";
                $pesanAdmin .= "👤 *Nama:* {$namaMember}\n";
                $pesanAdmin .= "📄 *Invoice:* #{$kode}\n";
                $pesanAdmin .= "📦 *Paket:* {$namaPaket}\n";
                $pesanAdmin .= "💰 *Total:* Rp {$total}\n";
                $pesanAdmin .= "🏦 *Bank:* {$request->nama_bank} ({$request->nama_rekening})\n\n";
                $pesanAdmin .= "Silakan cek bukti pembayaran dan verifikasi segera. 🚀";

                \App\Helpers\WhatsappHelper::send($noAdmin, $pesanAdmin);
            }
        } catch (\Exception $e) {
            // Biarkan saja jika gagal kirim WA agar proses upload user tidak terhenti
        }
        // --- LOGIKA NOTIFIKASI ADMIN END ---

        return redirect('/pembayaran/' . $kode)->with('success', 'Bukti berhasil dikirim! Admin akan segera memverifikasi.');
    }
    public function batal($kode)
    {
        $transaksi = Transaksi::where('kode_invoice', $kode)->firstOrFail();

        // Opsi A: Hapus data (Jika tidak ingin menumpuk sampah)
        $transaksi->member()->delete();
        $transaksi->delete();

        // Opsi B: Ubah status jadi Batal (Lebih aman untuk audit)
        // $transaksi->update(['status' => 'batal']);

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
