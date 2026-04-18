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
        // 1. Inisialisasi Query dengan Filter Default: Onsite & Hari Ini
        $query = Transaksi::with(['member', 'paket'])
            ->where('channel', 'onsite') // Hanya menampilkan yang Onsite
            ->latest();

        // 2. Logika Auto-Reset Harian
        // Jika user TIDAK sedang mencari berdasarkan tanggal, maka otomatis filter 'hari ini'
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereDate('created_at', today());
        }

        // 3. Filter Pencarian (Nama/Invoice) - Tetap berfungsi jika ingin cari data hari ini
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_invoice', 'like', "%{$search}%")
                    ->orWhere('nama_tamu', 'like', "%{$search}%")
                    ->orWhereHas('member', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
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
        $data    = $query->paginate(15)->withQueryString();
        $members = Member::orderBy('nama')->get();

        // Ambil paket selain harian untuk dropdown membership
        $paket = Paket::where('nama_paket', '!=', 'Harian')->get();
        // Ambil paket harian sebagai default
        $paketDefault = Paket::where('nama_paket', 'Harian')->first();

        // 7. Data untuk Summary Cards (Statistik)
        // total hari ini ambil data status di bayar dan chanlenya onsite
        $totalHariIni = Transaksi::whereDate('created_at', today())->where('status', 'dibayar')->where('channel', 'onsite')->sum('jumlah_bayar');
        $totalBulanIni = Transaksi::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('jumlah_bayar');
        $countHariIni  = Transaksi::whereDate('created_at', today())->where('status', 'dibayar')->count();

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
            'kode_invoice'       => 'INV-' . strtoupper(Str::random(6)),
            'nama_tamu'          => $request->nama_tamu,
            'paket_id'           => $paket->id,
            'tipe'               => 'harian',
            'channel'            => 'onsite',
            'jumlah_bayar'       => $paket->harga,
            'metode_pembayaran'  => $request->metode_pembayaran,
            'status'             => 'dibayar',
            'tanggal_pembayaran' => now(),
        ]);

        return back()->with('success', 'Transaksi harian berhasil dicatat');
    }

    public function storeMembership(Request $request)
    {
        // 1. Bersihkan input no_wa dari spasi, strip, atau tanda + sebelum divalidasi
        $request->merge([
            'no_wa' => $request->no_wa ? str_replace([' ', '-', '+'], '', $request->no_wa) : null,
        ]);

        // 2. Validasi Input
        $request->validate([
            'tipe_member'       => 'required|in:baru,perpanjang',
            'paket_id'          => 'required|exists:pakets,id',
            'metode_pembayaran' => 'required|in:cash,transfer', // Tambahkan validasi metode bayar

            // Validasi HP Indonesia: Wajib angka, awalan 08/628, panjang 10-15 digit
            'no_wa' => [
                'required_if:tipe_member,baru',
                'nullable',
                'unique:members,no_wa',
                'regex:/^(08|628)[0-9]{8,13}$/',
            ],
            'nama'          => 'required_if:tipe_member,baru',
            'jenis_kelamin' => 'required_if:tipe_member,baru',
            'member_id'     => 'nullable|required_if:tipe_member,perpanjang|exists:members,id',
        ], [
            'no_wa.unique'      => 'Waduh, nomor WhatsApp ini sudah terdaftar sebagai member! Silakan pilih menu perpanjang.',
            'no_wa.required_if' => 'Nomor WhatsApp wajib diisi untuk pendaftaran member baru.',
            'no_wa.regex'       => 'Format nomor tidak valid. Gunakan awalan 08 atau 628.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran (Cash/Transfer) dulu gess.',
        ]);

        if ($request->tipe_member === 'perpanjang') {
            $member = Member::findOrFail($request->member_id);

            // CEK STATUS: Jika nonaktif, batalkan proses
            if ($member->status === 'nonaktif') {
                return back()->with('error', 'Gagal: Member ini sedang dinonaktifkan (bermasalah). Pergi ke halaman management member.');
            }
        }

        try {
            return DB::transaction(function () use ($request) {
                $paket = Paket::findOrFail($request->paket_id);

                // 2. Logika Member (Baru atau Ambil yang sudah ada)
                if ($request->tipe_member === 'baru') {
                    $member = Member::create([
                        'kode_member'    => 'GYM-' . rand(1000, 9999),
                        'nama'           => $request->nama,
                        'no_wa'          => $request->no_wa,
                        'jenis_kelamin'  => $request->jenis_kelamin,
                        'status'         => 'aktif',
                        'tanggal_daftar' => now(),
                    ]);
                } else {
                    $member = Member::findOrFail($request->member_id);
                }

                // 3. Hitung Tanggal Mulai & Selesai
                $start = now();
                // Jika member masih punya masa aktif (belum kadaluarsa), akumulasikan
                if ($member->tanggal_kadaluarsa && Carbon::parse($member->tanggal_kadaluarsa)->gt(now())) {
                    $start = Carbon::parse($member->tanggal_kadaluarsa);
                }

                $end = Carbon::parse($start)->addDays($paket->durasi_hari);

                // 4. Buat Transaksi
                $transaksi = Transaksi::create([
                    'kode_invoice'       => 'INV-' . strtoupper(Str::random(6)),
                    'member_id'          => $member->id,
                    'paket_id'           => $paket->id,
                    'tipe'               => 'membership',
                    'channel'            => 'onsite',
                    'jumlah_bayar'       => $paket->harga,
                    'metode_pembayaran'  => $request->metode_pembayaran,
                    'status'             => 'dibayar',
                    'tanggal_pembayaran' => now(),
                ]);

                // 5. Catat Log Membership
                Membership::create([
                    'member_id'       => $member->id,
                    'transaksi_id'    => $transaksi->id,
                    'paket_id'        => $paket->id,
                    'tanggal_mulai'   => $start,
                    'tanggal_selesai' => $end,
                    'status'          => 'aktif',
                ]);

                // 6. Update Masa Aktif Member
                $member->update([
                    'status'             => 'aktif',
                    'tanggal_kadaluarsa' => $end
                ]);

                return back()->with('success', 'Membership ' . $member->nama . ' berhasil diproses');
            });
        } catch (\Exception $e) {
            // Jangan tampilkan $e->getMessage() kalau tidak mau lihat tulisan SQL yang rumit
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Pastikan semua input benar.')->withInput();
        }
    }
    // 1. Method Batalkan Transaksi
    public function batalkan($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // Proteksi: Hanya transaksi 'dibayar' yang bisa dibatalkan
        if ($transaksi->status === 'batal') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        try {
            return DB::transaction(function () use ($transaksi) {
                // Jika ini Membership, kita harus tarik ulang masa aktif membernya
                if ($transaksi->tipe === 'membership' && $transaksi->member_id) {
                    $member = Member::find($transaksi->member_id);

                    // Logika sederhana: set status nonaktif jika tidak ada membership lain
                    // Atau set kadaluarsa ke hari ini (mengurangi durasi paket yang dibatalkan)
                    $paket = $transaksi->paket;
                    $newExpired = Carbon::parse($member->tanggal_kadaluarsa)->subDays($paket->durasi_hari);

                    $member->update([
                        'tanggal_kadaluarsa' => $newExpired->lt(now()) ? now() : $newExpired,
                        'status' => $newExpired->lt(now()) ? 'nonaktif' : 'aktif'
                    ]);

                    // Nonaktifkan log di tabel memberships
                    Membership::where('transaksi_id', $transaksi->id)->update(['status' => 'batal']);
                }

                // Update status transaksi jadi dibatalkan
                $transaksi->update(['status' => 'batal']);

                return back()->with('success', 'Transaksi #' . $transaksi->kode_invoice . ' berhasil dibatalkan.');
            });
        } catch (\Exception $e) {
            // Tambahkan dd($e->getMessage()) untuk melihat error aslinya saat testing
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // 2. Method Cetak Struk (View Struk Digital)
    public function struk($id)
    {
        $transaksi = Transaksi::with(['member', 'paket'])->findOrFail($id);

        // Kita return view khusus struk yang ukurannya kecil (thermal)
        $safeInvoice = str_replace(['/', '\\'], '-', $transaksi->kode_invoice);
        $filename = 'STRUK-' . $safeInvoice . '.pdf';
        return view('transaksi.struk', compact('transaksi','filename'));
    }
}
