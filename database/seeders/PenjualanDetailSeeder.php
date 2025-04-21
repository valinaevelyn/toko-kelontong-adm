<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Str;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $itemIds = Item::pluck('id')->toArray();

        if (empty($itemIds))
            return;

        // Buat 20 transaksi penjualan fiktif
        for ($i = 0; $i < 20; $i++) {
            $tanggal = now();
            $prefix = 'F' . $tanggal->format('Ym');
            $noFaktur = $prefix . '-' . strtoupper(Str::random(6));

            $penjualan = Penjualan::create([
                'no_faktur' => $noFaktur,
                'nama_pembeli' => $faker->name,
                'tanggal_penjualan' => $tanggal,
                'total_harga_akhir' => 0,
                'total_item' => 0,
                'total_uang' => 0,
                'kembalian' => 0,
                'metode' => $faker->randomElement(['CASH', 'TRANSFER', 'KREDIT', 'CEK']),
                'status' => 'BELUM LUNAS',
            ]);

            $totalHarga = 0;
            $totalItem = 0;

            // Tambahkan 1–5 item ke penjualan
            $jumlahItem = $faker->numberBetween(1, 5);
            $selectedItems = $faker->randomElements($itemIds, $jumlahItem);

            foreach ($selectedItems as $itemId) {
                $item = Item::find($itemId);

                $stockDus = $faker->numberBetween(0, 3);
                $stockRcg = $faker->numberBetween(0, 5);
                $stockPcs = $faker->numberBetween(0, 20);

                $jumlahPcs = ($stockDus * $item->dus_in_pcs) + ($stockRcg * $item->rcg_in_pcs) + $stockPcs;
                $hargaSatuan = $faker->numberBetween(500, 5000);
                $subtotal = $jumlahPcs * $hargaSatuan;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'item_id' => $itemId,
                    'jumlah_dus' => $stockDus,
                    'jumlah_rcg' => $stockRcg,
                    'jumlah_pcs' => $stockPcs,
                    'jumlah' => $jumlahPcs,
                    'harga_satuan' => $hargaSatuan,
                ]);

                // Update stok item
                $item->increment('stock_dus', $stockDus);
                $item->increment('stock_rcg', $stockRcg);
                $item->increment('stock_pcs', $stockPcs);

                $totalHarga += $subtotal;
                $totalItem += $jumlahPcs;
            }

            // Update total di penjualan
            $penjualan->update([
                'total_harga_akhir' => $totalHarga,
                'total_item' => $totalItem,
            ]);
        }
    }
}
