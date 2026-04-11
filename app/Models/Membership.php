<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $table = 'memberships';

    protected $fillable = [
        'member_id',
        'transaksi_id',
        'paket_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];

    // Relasi
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }
}