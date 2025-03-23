<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\PenjualanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PenjualanController::class, 'index']);
Route::resource('/item', ItemController::class);
Route::resource('/penjualan', PenjualanController::class);
Route::put('/penjualan/pelunasan/{id}', [PenjualanController::class, 'pelunasan'])->name('penjualan.pelunasan');
Route::get('/penjualan/{id}/faktur', [PenjualanController::class, 'cetakFaktur'])->name('penjualan.faktur');
