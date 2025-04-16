<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPiutang extends Model
{
    protected $guarded = [];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
