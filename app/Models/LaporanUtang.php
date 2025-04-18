<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanUtang extends Model
{
    protected $guarded = [];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }
}
