<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            if ($detail->item) {
                $detail->total_harga = $detail->jumlah * $detail->item->harga;
            }
        });

        static::saved(function ($detail) {
            $detail->penjualan->updateTotalHarga();
        });

        static::deleted(function ($detail) {
            $detail->penjualan->updateTotalHarga();
        });
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
