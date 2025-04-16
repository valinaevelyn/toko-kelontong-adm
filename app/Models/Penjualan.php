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

    public function updateTotalHarga()
    {
        $this->total_harga_akhir = $this->penjualanDetails->sum('total_harga');
        $this->save();
    }

    public function laporanPiutang()
    {
        return $this->hasOne(LaporanPiutang::class);
    }

}
