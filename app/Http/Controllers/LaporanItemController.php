<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanItemController extends Controller
{
    public function laporanHistori(Request $request)
    {
        $tanggal = $request->input('tanggal') ?? date('Y-m-d');

        // Ambil semua item
        $items = DB::table('items')->get();

        $data = [];

        foreach ($items as $item) {
            // Jumlah pembelian item di tanggal tersebut
            $jumlah_pembelian = DB::table('pembelian_details')
                ->join('pembelians', 'pembelians.id', '=', 'pembelian_details.pembelian_id')
                ->whereDate('pembelians.tanggal_pembelian', $tanggal)
                ->where('pembelian_details.item_id', $item->id)
                ->sum('pembelian_details.jumlah');

            // Jumlah penjualan item di tanggal tersebut
            $jumlah_penjualan = DB::table('penjualan_details')
                ->join('penjualans', 'penjualans.id', '=', 'penjualan_details.penjualan_id')
                ->whereDate('penjualans.tanggal_penjualan', $tanggal)
                ->where('penjualan_details.item_id', $item->id)
                ->sum('penjualan_details.jumlah');

            // Masukkan ke array
            $data[] = [
                'tanggal' => $tanggal,
                'nama' => $item->nama,
                'merek' => $item->merek,
                'uom' => $item->uom,
                'pembelian' => $jumlah_pembelian,
                'penjualan' => $jumlah_penjualan,
                'sisa_stok' => $item->stock
            ];
        }

        return view('laporan.item', compact('data'));
    }
}
