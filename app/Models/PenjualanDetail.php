<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $guarded = [];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
