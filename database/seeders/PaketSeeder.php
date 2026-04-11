<?php

namespace Database\Seeders;

use App\Models\Paket;
use Illuminate\Database\Seeder;

class PaketSeeder extends Seeder
{
    public function run(): void
    {
        $pakets = [
            [
                'nama_paket'  => 'Harian',
                'harga'       => 20000,
                'durasi_hari' => 1,
                'deskripsi'   => 'Akses gym untuk satu hari.'
            ],
            [
                'nama_paket'  => 'Member Bulanan',
                'harga'       => 150000,
                'durasi_hari' => 30,
                'deskripsi'   => 'Akses gym sepuasnya selama 1 bulan.'
            ],
            [
                'nama_paket'  => 'Member 3 Bulan',
                'harga'       => 400000,
                'durasi_hari' => 90,
                'deskripsi'   => 'Paket hemat untuk 3 bulan akses.'
            ],
            [
                'nama_paket'  => 'Member Tahunan',
                'harga'       => 1400000,
                'durasi_hari' => 365,
                'deskripsi'   => 'Paket VIP akses selama setahun penuh.'
            ],
        ];

        foreach ($pakets as $paket) {
            Paket::create($paket);
        }
    }
}