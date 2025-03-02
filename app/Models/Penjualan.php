<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $guarded = [];

    // satu penjualan memiliki banyak detail penjualan
    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    // satu user bisa melakukan banyak penjualan
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
