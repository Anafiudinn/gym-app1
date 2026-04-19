<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class WhatsappHelper
{
    /**
     * Fungsi Kirim WA menggunakan Fonnte
     */
    public static function send($target, $message)
    {
        // Ambil data dari tabel settings yang kita buat kemarin
        $url = Setting::getValue('wa_api_url', 'https://api.fonnte.com/send');
        $token = Setting::getValue('wa_api_key');

        if (!$token) {
            return [
                'status' => false, 
                'message' => 'Token WA belum diatur di Pengaturan'
            ];
        }

       // Tambahkan method ->withoutVerifying()
$response = Http::withoutVerifying()->withHeaders([
    'Authorization' => $token,
])->post($url, [
    'target' => $target,
    'message' => $message,
    'countryCode' => '62',
]);

        return $response->json();
    }
}