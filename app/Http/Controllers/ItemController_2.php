<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController_2 extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('item.index', compact('items'));
    }
}
