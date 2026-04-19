<?php

namespace App\Helpers;

use App\Models\Setting;
use App\Models\WaLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WhatsappHelper
{
    /**
     * Fungsi Kirim WA menggunakan Fonnte
     */
    public static function send($target, $message)
    {
        // 1. Validasi awal: Kalau target kosong, jangan lanjut!
        if (empty($target)) {
            return [
                'status' => false,
                'message' => 'Gagal kirim: Nomor tujuan tidak ditemukan (NULL).'
            ];
        }
        // 2. LOGIKA KONVERSI: Ubah 08... jadi 628...
        // Kita hapus karakter selain angka (biar kalau ada spasi atau strip aman)
        $target = preg_replace('/[^0-9]/', '', $target);

        // Kalau depannya '0', ganti jadi '62'
        if (strpos($target, '0') === 0) {
            $target = '62' . substr($target, 1);
        }

        // 2. Ambil URL dan Token API
        $url = Setting::getValue('wa_api_url', 'https://api.fonnte.com/send');
        $token = Setting::getValue('wa_api_key');

        if (!$token) {
            return ['status' => false, 'message' => 'Token belum diatur'];
        }

        try {
            $response = Http::timeout(15) // Kita kasih napas 15 detik biar lebih lega
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post($url, [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $result = $response->json();

            // LOGGING: Catat setiap pengiriman (Berhasil/Gagal dari Fonnte)
            WaLog::create([
                'target' => $target,
                'message' => $message,
                'status' => ($result['status'] ?? false) ? 'success' : 'failed',
                'response' => json_encode($result)
            ]);

            return $result;

        } catch (\Exception $e) {
            // LOGGING: Catat kalau koneksi internet mati atau server Fonnte Down
            WaLog::create([
                'target' => $target,
                'message' => $message,
                'status' => 'failed',
                'response' => 'System Error: ' . $e->getMessage()
            ]);

            return [
                'status' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ];
        }
    }
    /**
     * Cek Koneksi Device ke Fonnte
     */
    public static function checkStatus()
    {
        // Gunakan durasi cache yang pendek saja saat masih tahap pengembangan (misal 10 detik)
        return Cache::remember('wa_status_connection', 200, function () {
            $token = \App\Models\Setting::getValue('wa_api_key');
            $apiUrl = \App\Models\Setting::getValue('wa_api_url', 'https://api.fonnte.com/send');

            // Transformasi URL: Mengubah .../send jadi .../device secara otomatis
            $baseUrl = str_replace('/send', '/device', $apiUrl);

            if (!$token)
                return false;

            try {
                $response = Http::timeout(5)->withoutVerifying()->withHeaders([
                    'Authorization' => $token
                ])->post($baseUrl);

                if ($response->successful()) {
                    $data = $response->json();
                    $deviceStatus = strtolower($data['device_status'] ?? ($data['data']['device_status'] ?? ''));

                    // Cek apakah ada kata 'connect' di statusnya
                    return str_contains($deviceStatus, 'connect');
                }
                return false;
            } catch (\Exception $e) {
                return false;
            }
        });
    }
}