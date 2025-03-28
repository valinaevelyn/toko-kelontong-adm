<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $total_pendapatan = Penjualan::sum('total_harga_akhir');
        $total_pengeluaran = Pembelian::sum('total_harga');
        $saldo_kas = $total_pendapatan - $total_pengeluaran;
        $keuntungan_bersih = $total_pendapatan - $total_pengeluaran;

        // Transaksi terbaru
        $transaksi_terbaru = \DB::table(function ($query) {
            $query->select(
                'tanggal_penjualan as tanggal',
                'total_harga_akhir as jumlah',
                \DB::raw("'Penjualan' as kategori"),
                \DB::raw("'Masuk' as jenis")
            )
                ->from('penjualans')
                ->unionAll(
                    \DB::table('pembelians')->select(
                        'tanggal_pembelian as tanggal',
                        'total_harga as jumlah',
                        \DB::raw("'Pembelian' as kategori"),
                        \DB::raw("'Keluar' as jenis")
                    )
                );
        }, 'transaksi')
            ->select('kategori', 'jenis', 'tanggal', 'jumlah') // Urutkan sesuai kebutuhan
            ->orderByDesc('tanggal')
            ->limit(5)
            ->get();

        return view('index', compact('total_pendapatan', 'total_pengeluaran', 'saldo_kas', 'keuntungan_bersih', 'transaksi_terbaru'));
    }
}
