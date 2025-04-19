<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanItem extends Model
{
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
