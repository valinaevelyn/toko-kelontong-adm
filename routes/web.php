<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LaporanBankController;
use App\Http\Controllers\LaporanKasController;
use App\Http\Controllers\LaporanPiutangController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/', [LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/item', ItemController::class);

    Route::resource('/penjualan', PenjualanController::class);
    Route::put('/penjualan/pelunasan/{id}', [PenjualanController::class, 'pelunasan'])->name('penjualan.pelunasan');
    Route::get('/penjualan/{id}/faktur', [PenjualanController::class, 'cetakFaktur'])->name('penjualan.faktur');

    Route::resource('/pembelian', PembelianController::class);
    Route::put('/pembelian/pelunasan/{id}', [PembelianController::class, 'pelunasan'])->name('pembelian.pelunasan');
    Route::get('/pembelian/{id}/faktur', [PembelianController::class, 'cetakFaktur'])->name('pembelian.faktur');


    Route::post('/dashboard/transfer-saldo', [DashboardController::class, 'transferSaldo'])->name('dashboard.transferSaldo');
});

Route::middleware(['supervisor'])->group(function () {
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/laporan-kas', [LaporanKasController::class, 'index'])->name('laporan.kas');
    Route::post('/laporan-kas/biaya', [LaporanKasController::class, 'storeBiaya'])->name('laporan.kas.biaya');

    Route::get('/laporan-piutang', [LaporanPiutangController::class, 'index'])->name('laporan.piutang');
    Route::post('/laporan-piutang/biaya', [LaporanPiutangController::class, 'storePiutang'])->name('laporan.piutang.biaya');

    Route::get('/laporan-bank', [LaporanBankController::class, 'index'])->name('laporan.bank');
    Route::post('/laporan-bank/biaya', [LaporanBankController::class, 'storeBiaya'])->name('laporan.bank.biaya');
});
