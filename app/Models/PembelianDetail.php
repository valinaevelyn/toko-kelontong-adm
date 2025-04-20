<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // static::saving(function ($detail) {
        //     if ($detail->item) {
        //         $detail->total_harga = $detail->jumlah * $detail->item->harga_beli;
        //     }
        // });

        static::saved(function ($detail) {
            $detail->pembelian->updateTotalHarga();
        });

        static::deleted(function ($detail) {
            $detail->pembelian->updateTotalHarga();
        });
    }

    // aku maunya ketika transaksi pembelian item, item yang dibeli akan masuk ke items

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

}
