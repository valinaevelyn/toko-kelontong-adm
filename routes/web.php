<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LaporanKasController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);
Route::resource('/item', ItemController::class);

Route::resource('/penjualan', PenjualanController::class);
Route::put('/penjualan/pelunasan/{id}', [PenjualanController::class, 'pelunasan'])->name('penjualan.pelunasan');
Route::get('/penjualan/{id}/faktur', [PenjualanController::class, 'cetakFaktur'])->name('penjualan.faktur');

Route::resource('/pembelian', PembelianController::class);
Route::put('/pembelian/pelunasan/{id}', [PembelianController::class, 'pelunasan'])->name('pembelian.pelunasan');
Route::get('/pembelian/{id}/faktur', [PembelianController::class, 'cetakFaktur'])->name('pembelian.faktur');

Route::get('/laporan-kas', [LaporanKasController::class, 'index'])->name('laporan.kas');
Route::post('/laporan-kas/biaya', [LaporanKasController::class, 'storeBiaya'])->name('laporan.kas.biaya');
