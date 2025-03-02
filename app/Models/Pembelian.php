<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $guarded = [];

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
}
