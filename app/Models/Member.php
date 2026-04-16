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

    public function memberships()
    {
        return $this->hasMany(Membership::class, 'member_id');
    }
    public function membership()
{
    // Ini akan mengambil 1 data membership terbaru milik si member
    return $this->hasOne(Membership::class)->latestOfMany();
}

    // Relasi untuk mengambil paket yang AKTIF saja (opsional, buat dashboard)
    public function membershipAktif()
    {
        return $this->hasOne(Membership::class)->latestOfMany();
    }
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
