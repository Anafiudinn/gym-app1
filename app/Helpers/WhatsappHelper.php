<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class WhatsappHelper
{
    /**
     * Fungsi Kirim WA menggunakan Fonnte
     */
    public static function send($target, $message)
    {
        $url = Setting::getValue('wa_api_url', 'https://api.fonnte.com/send');
        $token = Setting::getValue('wa_api_key');

        if (!$token) {
            return ['status' => false, 'message' => 'Token belum diatur'];
        }

        try {
            // Kita tambahkan timeout(10) artinya Laravel cuma mau nunggu 10 detik
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post($url, [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            return $response->json();

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Kalau Fonnte nggak balas dalam 10 detik, masuk ke sini
            return [
                'status' => false,
                'message' => 'Koneksi ke Fonnte terputus atau sangat lambat.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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