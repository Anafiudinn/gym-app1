<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Membership;
use App\Models\Paket;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Query (Tetap sama)
        $query = Transaksi::with(['member', 'paket'])
            ->where('channel', 'onsite')
            ->latest();

        // 2 & 3. LOGIKA SMART FILTER (Kuncinya di sini!)
        if ($request->filled('search')) {
            // JIKA ADA SEARCH: Abaikan filter tanggal hari ini, cari di SEMUA riwayat
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_invoice', 'like', "%{$search}%")
                    ->orWhere('nama_tamu', 'like', "%{$search}%")
                    ->orWhereHas('member', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        } else {
            // JIKA TIDAK ADA SEARCH: Baru terapkan filter tanggal
            if (!$request->filled('date_from') && !$request->filled('date_to')) {
                // Default: Tampilkan hari ini jika filter tanggal kosong
                $query->whereDate('created_at', today());
            } else {
                // Filter tanggal manual jika diisi
                if ($request->filled('date_from')) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
            }
        }
        // 4. Filter Tanggal Manual (Jika ingin melihat history hari lain)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 5. Filter Tipe & Status
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 6. Eksekusi Data
        $data = $query->paginate(20)->withQueryString();
        $members = Member::orderBy('nama')->get();

        // Ambil paket selain harian untuk dropdown membership
        $paket = Paket::where('nama_paket', '!=', 'Harian')->get();
        // Ambil paket harian sebagai default
        $paketDefault = Paket::where('nama_paket', 'Harian')->first();

        // 7. Data untuk Summary Cards (Statistik)
        // total hari ini ambil data status di bayar dan chanlenya onsite
        $totalHariIni = Transaksi::whereDate('created_at', today())->where('status', 'dibayar')->where('channel', 'onsite')->sum('jumlah_bayar');
        $totalBulanIni = Transaksi::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('jumlah_bayar');
        $countHariIni = Transaksi::whereDate('created_at', today())->where('status', 'dibayar')->count();

        $activeTab = $request->query('tab', 'tamu');
        $selectedMemberId = $request->query('member');

        // 8. Kirim ke View
        return view('transaksi.index', compact(
            'data',
            'members',
            'paket',
            'selectedMemberId',
            'activeTab',
            'totalHariIni',
            'totalBulanIni',
            'countHariIni',
            'paketDefault'
        ));
    }

    public function storeHarian(Request $request)
    {
        $request->validate(
            [
                'nama_tamu' => 'required',
                'metode_pembayaran' => 'required|in:cash,transfer'
            ],
            [
                'nama_tamu.required' => 'Nama pengunjung wajib diisi untuk transaksi harian.',
                'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih untuk transaksi harian.',
                'metode_pembayaran.in' => 'Metode pembayaran yang dipilih tidak ditemukan. Silahkan pilih salah satu metode pembayaran yang tersedia.',

            ]
        );

        $paket = Paket::where('nama_paket', 'Harian')->first();


        if (!$paket) {
            return back()->with('error', 'Gagal: Paket bernama "Harian" belum dibuat di database!');
        }

        Transaksi::create([
            'kode_invoice' => 'INV-' . strtoupper(Str::random(6)),
            'nama_tamu' => $request->nama_tamu,
            'paket_id' => $paket->id,
            'tipe' => 'harian',
            'channel' => 'onsite',
            'jumlah_bayar' => $paket->harga,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => 'dibayar',
            'tanggal_pembayaran' => now(),
        ]);

        return back()->with('success', 'Transaksi harian berhasil dicatat');
    }

    public function storeMembership(Request $request)
    {
        // 1. Bersihkan input no_wa
        $request->merge([
            'no_wa' => $request->no_wa ? str_replace([' ', '-', '+'], '', $request->no_wa) : null,
        ]);

        // 2. Validasi Input
        $request->validate([
            'tipe_member' => 'required|in:baru,perpanjang',
            'paket_id' => 'required|exists:pakets,id',
            'metode_pembayaran' => 'required|in:cash,transfer',
            'no_wa' => [
                'required_if:tipe_member,baru',
                'nullable',
                'unique:members,no_wa',
                'regex:/^(08|628)[0-9]{8,13}$/',
            ],
            'nama' => 'required_if:tipe_member,baru',
            'jenis_kelamin' => 'required_if:tipe_member,baru',
            'member_id' => 'nullable|required_if:tipe_member,perpanjang|exists:members,id',
        ], [
            'no_wa.unique' => 'Nomor WA sudah terdaftar! Gunakan menu perpanjang gess.',
            'no_wa.regex' => 'Format nomor tidak valid. Gunakan awalan 08 atau 628.',
        ]);

        // --- BAGIAN INI SUDAH KITA HAPUS (Proteksi Nonaktif Dibuang) ---
        // Jadi siapa pun bisa diperpanjang untuk "Aktivasi Ulang"

        try {
            return DB::transaction(function () use ($request) {
                $paket = Paket::findOrFail($request->paket_id);

                // 3. Logika Member (Baru atau Ambil yang sudah ada)
                if ($request->tipe_member === 'baru') {
                    $member = Member::create([
                        'kode_member' => 'GYM-' . rand(1000, 9999),
                        'nama' => $request->nama,
                        'no_wa' => $request->no_wa,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'status' => 'aktif',
                        'tanggal_daftar' => now(),
                    ]);
                } else {
                    $member = Member::findOrFail($request->member_id);
                }

                // 4. Hitung Tanggal Mulai & Selesai
                $start = now();

                // Cek apakah member punya tanggal kadaluarsa dan apakah masih aktif/berlaku sampai besok atau lebih
                if ($member->tanggal_kadaluarsa) {
                    $kadaluarsa = Carbon::parse($member->tanggal_kadaluarsa);

                    // Jika kadaluarsanya hari ini atau masa depan, maka sambung dari sana
                    // Kita gunakan isFuture() atau isToday() supaya aman
                    if ($kadaluarsa->isFuture() || $kadaluarsa->isToday()) {
                        $start = $kadaluarsa;
                    }
                }

                // Gunakan copy() supaya variabel $start asli tidak ikut berubah saat ditambah days
                $end = (clone $start)->addDays($paket->durasi_hari);
                
                // 5. Buat Transaksi
                $transaksi = Transaksi::create([
                    'kode_invoice' => 'INV-' . strtoupper(Str::random(6)),
                    'member_id' => $member->id,
                    'paket_id' => $paket->id,
                    'tipe' => 'membership',
                    'channel' => 'onsite',
                    'jumlah_bayar' => $paket->harga,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'status' => 'dibayar',
                    'tanggal_pembayaran' => now(),
                ]);

                // 6. Catat Log Membership
                Membership::create([
                    'member_id' => $member->id,
                    'transaksi_id' => $transaksi->id,
                    'paket_id' => $paket->id,
                    'tanggal_mulai' => $start,
                    'tanggal_selesai' => $end,
                    'status' => 'aktif',
                ]);

                // 7. Update Masa Aktif Member (OTOMATIS JADI AKTIF LAGI)
                $member->update([
                    'status' => 'aktif',
                    'tanggal_kadaluarsa' => $end
                ]);

                // --- LOGIC NOTIFIKASI WA PRO ---
                $namaGym = \App\Models\Setting::getValue('nama_gym', 'Gym Fit');

                $pesan = "*TRANSAKSI BERHASIL* ✅\n\n";
                $pesan .= "Halo *{$member->nama}*,\n";
                $pesan .= ($request->tipe_member === 'baru')
                    ? "Selamat bergabung di *{$namaGym}*!\n\n"
                    : "Terima kasih telah memperpanjang membership di *{$namaGym}*!\n\n";

                $pesan .= "--- *Detail Membership* ---\n";
                $pesan .= "ID Member : *{$member->kode_member}*\n";
                $pesan .= "Paket     : {$paket->nama_paket}\n";
                $pesan .= "Masa Aktif: " . $end->format('d M Y') . "\n";
                $pesan .= "Metode    : " . strtoupper($request->metode_pembayaran) . "\n\n";
                $pesan .= "Semangat latihannya gess! 🔥";

                \App\Helpers\WhatsappHelper::send($member->no_wa, $pesan);

                return back()->with('success', 'Membership ' . $member->nama . ' berhasil diproses & WA terkirim!');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    // 1. Method Batalkan Transaksi
    public function batalkan($id)
    {
        $transaksi = Transaksi::with(['member', 'paket'])->findOrFail($id);

        // Proteksi: Hanya transaksi yang sudah dibayar yang bisa dibatalkan
        if ($transaksi->status === 'batal') {
            return back()->with('error', 'Waduh, transaksi ini memang sudah batal dari awal gess.');
        }

        try {
            return DB::transaction(function () use ($transaksi) {
                $member = $transaksi->member;
                $paket = $transaksi->paket;

                // 1. LOGIKA KOREKSI MASA AKTIF (Jika transaksi membership)
                if ($transaksi->tipe === 'membership' && $member) {
                    // Ambil tanggal kadaluarsa saat ini
                    $currentExpired = Carbon::parse($member->tanggal_kadaluarsa);

                    // KURANGI dengan durasi paket yang dibatalkan
                    $newExpired = $currentExpired->subDays($paket->durasi_hari);

                    // Cek apakah setelah dikurangi dia masih punya sisa hari di masa depan?
                    $isStillActive = $newExpired->gt(now());

                    // Update Member: Jika newExpired sudah lewat, kita mentokin di 'now' agar tidak minus jauh ke belakang
                    $member->update([
                        'tanggal_kadaluarsa' => $newExpired->lt(now()) ? now() : $newExpired,
                        'status' => $isStillActive ? 'aktif' : 'nonaktif'
                    ]);

                    // Update log di tabel memberships (History perpanjangan jadi batal)
                    Membership::where('transaksi_id', $transaksi->id)->update(['status' => 'batal']);
                }

                // 2. UPDATE STATUS TRANSAKSI UTAMA
                $transaksi->update([
                    'status' => 'batal',
                    'keterangan' => 'Dibatalkan oleh admin pada ' . now()->format('d-m-Y H:i')
                ]);

                // 3. KIRIM NOTIFIKASI WA (BIAR PRO)
                if ($member && $member->no_wa) {
                    $namaGym = \App\Models\Setting::getValue('nama_gym', 'Gym Kami');

                    $pesan = "*PEMBATALAN TRANSAKSI* ⚠️\n\n";
                    $pesan .= "Halo *{$member->nama}*,\n";
                    $pesan .= "Transaksi *#{$transaksi->kode_invoice}* telah dibatalkan untuk keperluan koreksi data.\n\n";
                    $pesan .= "--- *Update Status* ---\n";
                    $pesan .= "ID Member: *{$member->kode_member}*\n";
                    $pesan .= "Masa Aktif: *" . Carbon::parse($member->tanggal_kadaluarsa)->format('d M Y') . "*\n";
                    $pesan .= "Status Akun: " . strtoupper($member->status) . "\n\n";
                    $pesan .= "Silakan hubungi admin jika ingin melakukan perpanjangan ulang. Terima kasih.";

                    \App\Helpers\WhatsappHelper::send($member->no_wa, $pesan);
                }

                return back()->with('success', 'Transaksi #' . $transaksi->kode_invoice . ' berhasil dibatalkan. Masa aktif member telah disesuaikan.');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
    // 2. Method Cetak Struk (View Struk Digital)
    public function struk($id)
    {
        $transaksi = Transaksi::with(['member', 'paket'])->findOrFail($id);

        // Kita return view khusus struk yang ukurannya kecil (thermal)
        $safeInvoice = str_replace(['/', '\\'], '-', $transaksi->kode_invoice);
        $filename = 'STRUK-' . $safeInvoice . '.pdf';
        return view('transaksi.struk', compact('transaksi', 'filename'));
    }
}
