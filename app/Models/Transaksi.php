<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';

    protected $fillable = [
        'kode_invoice',
        'member_id',
        'nama_tamu',
        'paket_id',
        'tipe',
        'jumlah_bayar',
        'metode_pembayaran',
        'status',
        'tanggal_pembayaran'
    ];

    // Relasi
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    public function verifikasi()
    {
        return $this->hasOne(VerifikasiPembayaran::class, 'transaksi_id');
    }

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }
}