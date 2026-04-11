<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';

    protected $fillable = [
        'kode_member',
        'nama',
        'no_wa',
        'jenis_kelamin',
        'status',
        'tanggal_daftar',
        'tanggal_kadaluarsa'
    ];

    // TAMBAHKAN INI GESS!
    protected $casts = [
        'tanggal_daftar' => 'datetime',
        'tanggal_kadaluarsa' => 'datetime',
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

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}