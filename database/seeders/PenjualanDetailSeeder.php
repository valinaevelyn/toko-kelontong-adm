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
                'metode' => $metode = $faker->randomElement(['CASH', 'TRANSFER', 'KREDIT', 'CEK']),
                'status' => 'BELUM LUNAS',
            ]);

            $totalHarga = 0;
            $totalItem = 0;

            $jumlahItem = $faker->numberBetween(1, 5);
            $selectedItems = $faker->randomElements($itemIds, $jumlahItem);

            foreach ($selectedItems as $itemId) {
                $item = Item::find($itemId);

                if (!$item || $item->dus_in_pcs <= 0 || $item->rcg_in_pcs <= 0)
                    continue;

                $jumlahDus = $faker->numberBetween(0, 3);
                $jumlahRcg = $faker->numberBetween(0, 5);
                $jumlahPcs = $faker->numberBetween(0, 20);

                $jumlahTotalPcs = ($jumlahDus * $item->dus_in_pcs) + ($jumlahRcg * $item->rcg_in_pcs) + $jumlahPcs;
                $hargaSatuan = $faker->numberBetween(500, 5000);
                $subtotal = $jumlahTotalPcs * $hargaSatuan;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'item_id' => $itemId,
                    'jumlah_dus' => $jumlahDus,
                    'jumlah_rcg' => $jumlahRcg,
                    'jumlah_pcs' => $jumlahPcs,
                    'jumlah' => $jumlahTotalPcs,
                    'harga_satuan' => $hargaSatuan,
                ]);

                // Kurangi stok item sesuai penjualan
                $item->decrement('stock_dus', $jumlahDus);
                $item->decrement('stock_rcg', $jumlahRcg);
                $item->decrement('stock_pcs', $jumlahPcs);

                $totalHarga += $subtotal;
                $totalItem += $jumlahTotalPcs;
            }

            // Asumsikan jika metode CASH/TRANSFER langsung dibayar penuh
            $totalUang = in_array($metode, ['CASH', 'TRANSFER']) ? $totalHarga : 0;
            $kembalian = max($totalUang - $totalHarga, 0);
            $status = $totalUang >= $totalHarga ? 'LUNAS' : 'BELUM LUNAS';

            $penjualan->update([
                'total_harga_akhir' => $totalHarga,
                'total_item' => $totalItem,
                'total_uang' => $totalUang,
                'kembalian' => $kembalian,
                'status' => $status,
            ]);
        }
    }
}
