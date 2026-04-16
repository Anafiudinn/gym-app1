<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $table = 'pakets';

    protected $fillable = [
        'nama_paket',
        'harga',
        'durasi_hari',
        'deskripsi'
    ];

    // Relasi
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function membership()
    {
        return $this->hasMany(Membership::class);
    }
  
}
