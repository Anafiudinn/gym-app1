<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absens';

    protected $fillable = [
        'member_id',
        'waktu_masuk',
        'status'
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
    ];

    // Relasi
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
