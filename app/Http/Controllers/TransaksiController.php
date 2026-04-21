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
        // 1. Inisialisasi Query
        $query = Transaksi::with(['member', 'paket'])
            ->where('channel', 'onsite')
            ->latest();

        // 2. LOGIKA SMART FILTER
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_invoice', 'like', "%{$search}%")
                    ->orWhere('nama_tamu', 'like', "%{$search}%")
                    ->orWhereHas('member', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        } else {
            if (!$request->filled('date_from') && !$request->filled('date_to')) {
                $query->whereDate('created_at', today());
            } else {
                if ($request->filled('date_from')) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
            }
        }

        // 3. Filter Tanggal Manual
        if ($request->filled('date_from') && $request->filled('search')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to') && $request->filled('search')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 4. Filter Tipe & Status
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 5. Eksekusi Data
        $data    = $query->paginate(20)->withQueryString();
        $members = Member::orderBy('nama')->get();
        $paket   = Paket::where('nama_paket', '!=', 'Harian')->get();
        $paketDefault = Paket::where('nama_paket', 'Harian')->first();

        // 6. Summary
        $totalHariIni  = Transaksi::whereDate('created_at', today())->where('status', 'dibayar')->where('channel', 'onsite')->sum('jumlah_bayar');
        $totalBulanIni = Transaksi::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('jumlah_bayar');
        $countHariIni  = Transaksi::whereDate('created_at', today())->where('status', 'dibayar')->count();

        // ── TAB PERSISTENCE ──────────────────────────────────────────
        // Prioritas: 1) query param ?tab=  2) session flash dari form gagal  3) default 'tamu'
        $activeTab        = $request->query('tab', session('_last_tab', 'tamu'));
        $selectedMemberId = $request->query('member', session('_last_member_id'));

        // Bersihkan session flash setelah dibaca
        session()->forget(['_last_tab', '_last_member_id']);
        // ─────────────────────────────────────────────────────────────

        return view('transaksi.index', compact(
            'data', 'members', 'paket', 'selectedMemberId',
            'activeTab', 'totalHariIni', 'totalBulanIni',
            'countHariIni', 'paketDefault'
        ));
    }

    public function storeHarian(Request $request)
    {
        $request->validate(
            [
                'nama_tamu'         => 'required',
                'metode_pembayaran' => 'required|in:cash,transfer',
            ],
            [
                'nama_tamu.required'         => 'Nama pengunjung wajib diisi untuk transaksi harian.',
                'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
                'metode_pembayaran.in'       => 'Metode pembayaran tidak valid.',
            ]
        );

        $paket = Paket::where('nama_paket', 'Harian')->first();

        if (!$paket) {
            return back()->with('error', 'Gagal: Paket "Harian" belum dibuat di database!');
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
        // ── TAB PERSISTENCE: simpan ke session SEBELUM validasi ──────
        // Supaya jika validasi gagal dan Laravel redirect back(),
        // halaman tahu harus membuka tab mana lagi.
        $tabAsal  = $request->input('tab', 'tamu');
        $memberId = $request->input('member_id');

        session([
            '_last_tab'       => $tabAsal,
            '_last_member_id' => $memberId,
        ]);
        // ─────────────────────────────────────────────────────────────

        // 1. Bersihkan input no_wa
        $request->merge([
            'no_wa' => $request->no_wa
                ? str_replace([' ', '-', '+'], '', $request->no_wa)
                : null,
        ]);

        // 2. Validasi
        $request->validate([
            'tipe_member'       => 'required|in:baru,perpanjang',
            'paket_id'          => 'required|exists:pakets,id',
            'metode_pembayaran' => 'required|in:cash,transfer',
            'no_wa'             => [
                'required_if:tipe_member,baru',
                'nullable',
                'unique:members,no_wa',
                'regex:/^(08|628)[0-9]{8,13}$/',
            ],
            'nama'          => 'required_if:tipe_member,baru',
            'jenis_kelamin' => 'required_if:tipe_member,baru',
            'member_id'     => 'nullable|required_if:tipe_member,perpanjang|exists:members,id',
        ], [
            'no_wa.unique' => 'Nomor WA sudah terdaftar! Gunakan menu perpanjang.',
            'no_wa.regex'  => 'Format nomor tidak valid. Gunakan awalan 08 atau 628.',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $paket = Paket::findOrFail($request->paket_id);

                // 3. Member baru atau perpanjang
                if ($request->tipe_member === 'baru') {
                    $member = Member::create([
                        'kode_member'   => 'GYM-' . rand(1000, 9999),
                        'nama'          => $request->nama,
                        'no_wa'         => $request->no_wa,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'status'        => 'aktif',
                        'tanggal_daftar'=> now(),
                    ]);
                } else {
                    $member = Member::findOrFail($request->member_id);
                }

                // 4. Hitung tanggal mulai & selesai
                $start = now();
                if ($member->tanggal_kadaluarsa) {
                    $kadaluarsa = Carbon::parse($member->tanggal_kadaluarsa);
                    if ($kadaluarsa->isFuture() || $kadaluarsa->isToday()) {
                        $start = $kadaluarsa;
                    }
                }
                $end = (clone $start)->addDays($paket->durasi_hari);

                // 5. Buat Transaksi
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

                // 6. Log Membership
                Membership::create([
                    'member_id'       => $member->id,
                    'transaksi_id'    => $transaksi->id,
                    'paket_id'        => $paket->id,
                    'tanggal_mulai'   => $start,
                    'tanggal_selesai' => $end,
                    'status'          => 'aktif',
                ]);

                // 7. Update Member
                $member->update([
                    'status'             => 'aktif',
                    'tanggal_kadaluarsa' => $end,
                ]);

                // 8. Notifikasi WA
                $namaGym = \App\Models\Setting::getValue('nama_gym', 'Gym Fit');
                $pesan   = "*TRANSAKSI BERHASIL* ✅\n\n";
                $pesan  .= "Halo *{$member->nama}*,\n";
                $pesan  .= ($request->tipe_member === 'baru')
                    ? "Selamat bergabung di *{$namaGym}*!\n\n"
                    : "Terima kasih telah memperpanjang membership di *{$namaGym}*!\n\n";
                $pesan .= "--- *Detail Membership* ---\n";
                $pesan .= "ID Member : *{$member->kode_member}*\n";
                $pesan .= "Paket     : {$paket->nama_paket}\n";
                $pesan .= "Masa Aktif: " . $end->format('d M Y') . "\n";
                $pesan .= "Metode    : " . strtoupper($request->metode_pembayaran) . "\n\n";
                $pesan .= "Semangat latihannya! 🔥";

                \App\Helpers\WhatsappHelper::send($member->no_wa, $pesan);

                // ── Bersihkan session tab setelah SUKSES ─────────────
                session()->forget(['_last_tab', '_last_member_id']);
                // ─────────────────────────────────────────────────────

                return back()->with('success', 'Membership ' . $member->nama . ' berhasil diproses & WA terkirim!');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function batalkan($id)
    {
        $transaksi = Transaksi::with(['member', 'paket'])->findOrFail($id);

        if ($transaksi->status === 'batal') {
            return back()->with('error', 'Transaksi ini sudah batal sebelumnya.');
        }

        try {
            return DB::transaction(function () use ($transaksi) {
                $member = $transaksi->member;
                $paket  = $transaksi->paket;

                if ($transaksi->tipe === 'membership' && $member) {
                    $currentExpired = Carbon::parse($member->tanggal_kadaluarsa);
                    $newExpired     = $currentExpired->subDays($paket->durasi_hari);
                    $isStillActive  = $newExpired->gt(now());

                    $member->update([
                        'tanggal_kadaluarsa' => $newExpired->lt(now()) ? now() : $newExpired,
                        'status'             => $isStillActive ? 'aktif' : 'nonaktif',
                    ]);

                    Membership::where('transaksi_id', $transaksi->id)->update(['status' => 'batal']);
                }

                $transaksi->update([
                    'status'     => 'batal',
                    'keterangan' => 'Dibatalkan oleh admin pada ' . now()->format('d-m-Y H:i'),
                ]);

                if ($member && $member->no_wa) {
                    $namaGym = \App\Models\Setting::getValue('nama_gym', 'Gym Kami');
                    $pesan   = "*PEMBATALAN TRANSAKSI* ⚠️\n\n";
                    $pesan  .= "Halo *{$member->nama}*,\n";
                    $pesan  .= "Transaksi *#{$transaksi->kode_invoice}* telah dibatalkan.\n\n";
                    $pesan  .= "--- *Update Status* ---\n";
                    $pesan  .= "ID Member: *{$member->kode_member}*\n";
                    $pesan  .= "Masa Aktif: *" . Carbon::parse($member->tanggal_kadaluarsa)->format('d M Y') . "*\n";
                    $pesan  .= "Status Akun: " . strtoupper($member->status) . "\n\n";
                    $pesan  .= "Silakan hubungi admin untuk perpanjangan ulang. Terima kasih.";

                    \App\Helpers\WhatsappHelper::send($member->no_wa, $pesan);
                }

                return back()->with('success', 'Transaksi #' . $transaksi->kode_invoice . ' berhasil dibatalkan.');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    public function struk($id)
    {
        $transaksi   = Transaksi::with(['member', 'paket'])->findOrFail($id);
        $safeInvoice = str_replace(['/', '\\'], '-', $transaksi->kode_invoice);
        $filename    = 'STRUK-' . $safeInvoice . '.pdf';

        return view('transaksi.struk', compact('transaksi', 'filename'));
    }
}