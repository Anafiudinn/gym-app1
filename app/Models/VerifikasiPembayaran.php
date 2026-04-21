<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiPembayaran extends Model
{
    protected $table = 'verifikasi_pembayarans';

    protected $fillable = [
        'transaksi_id',
        'nama_rekening',
        'nama_bank',
        'bukti_pembayaran',
        'status',
        'catatan_admin',
        'diverifikasi_oleh',
        'tanggal_verifikasi'
    ];

    // Relasi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
    
}