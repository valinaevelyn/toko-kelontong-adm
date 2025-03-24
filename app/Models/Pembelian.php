<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $guarded = [];

    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class, 'pembelian_id');
    }

    // satu pembelian memiliki banyak item
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // satu user bisa melakukan banyak pembelian
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateTotalHarga()
    {
        $this->total_harga = $this->items->sum('total_harga');
        $this->save();
    }
}
