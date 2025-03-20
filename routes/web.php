<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\PenjualanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PenjualanController::class, 'index']);
Route::resource('/item', ItemController::class);