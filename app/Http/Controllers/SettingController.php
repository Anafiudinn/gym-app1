<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            // Cek jika key-nya adalah nomor telepon
            if ($key === 'no_telp' && !empty($value)) {
                // 1. Ambil angka saja
                $value = preg_replace('/[^0-9]/', '', $value);

                // 2. Jika awalannya 0, ubah jadi 62
                if (strpos($value, '0') === 0) {
                    $value = '62' . substr($value, 1);
                }

                // 3. Jika input mulai dari 8..., tambahkan 62
                if (strpos($value, '8') === 0) {
                    $value = '62' . $value;
                }
            }

            // Cek juga untuk Instagram agar tidak double URL
            if ($key === 'instagram' && !empty($value)) {
                // Jika user input link lengkap, kita ambil username-nya saja
                if (str_contains($value, 'instagram.com/')) {
                    $parts = explode('instagram.com/', $value);
                    $value = rtrim(end($parts), '/');
                }
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}