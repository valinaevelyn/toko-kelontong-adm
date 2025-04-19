<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        public function laporanItem(Request $request)
{
    $tanggal = $request->input('tanggal') ?? date('Y-m-d');

    // Ambil data pembelian (items masuk)
    $pembelian = DB::table('pembelian_details')
        ->join('pembelians', 'pembelians.id', '=', 'pembelian_details.pembelian_id')
        ->join('items', 'items.id', '=', 'pembelian_details.item_id')
        ->leftJoin('suppliers', 'suppliers.id', '=', 'pembelians.supplier_id') // optional, jika ada
        ->whereDate('pembelians.tanggal', $tanggal)
        ->select([
            'pembelians.tanggal as tanggal',
            'items.nama as nama_item',
            DB::raw("CONCAT('Pembelian oleh ', COALESCE(suppliers.nama, 'Tidak Diketahui')) as keterangan"),
            'pembelian_details.jumlah as jumlah',
            'pembelian_details.item_id'
        ]);

    // Ambil data penjualan (items keluar)
    $penjualan = DB::table('penjualan_details')
        ->join('penjualans', 'penjualans.id', '=', 'penjualan_details.penjualan_id')
        ->join('items', 'items.id', '=', 'penjualan_details.item_id')
        ->leftJoin('customers', 'customers.id', '=', 'penjualans.customer_id') // optional, jika ada
        ->whereDate('penjualans.tanggal', $tanggal)
        ->select([
            'penjualans.tanggal as tanggal',
            'items.nama as nama_item',
            DB::raw("CONCAT('Penjualan ke ', COALESCE(customers.nama, 'Tidak Diketahui')) as keterangan"),
            DB::raw("-1 * penjualan_details.jumlah as jumlah"),
            'penjualan_details.item_id'
        ]);

    // Gabungkan pembelian dan penjualan
    $laporan = $pembelian->unionAll($penjualan)->orderBy('tanggal')->get();

    // Tambahkan sisa stok dari tabel items
    $laporan->transform(function ($item) {
        $stok = DB::table('items')->where('id', $item->item_id)->value('stok');
        $item->sisa_stok = $stok;
        return $item;
    });

    return view('laporan.laporan_item', compact('laporan', 'tanggal'));
}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};
