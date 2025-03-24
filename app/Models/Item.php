<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];

    // satu item bisa dimiliki oleh banyak penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    // satu item bisa dimiliki oleh banyak pembelian
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }


}
