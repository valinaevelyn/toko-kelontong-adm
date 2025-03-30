<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\PembelianDetail;

class PembelianDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil semua item yang tersedia di database
        $itemIds = Item::pluck('id')->toArray();

        // Jika tidak ada item di database, hentikan seeding
        if (empty($itemIds)) {
            return;
        }

        // Buat daftar `penjualan_id` yang akan diisi
        $pembelianIds = range(1, 20);
        shuffle($pembelianIds); // Acak urutan

        foreach ($pembelianIds as $pembelianId) {
            // Pastikan setiap `penjualan_id` memiliki minimal satu item
            $jumlahItem = $faker->numberBetween(1, 5); // Maksimal 5 item per penjualan
            $selectedItems = $faker->randomElements($itemIds, $jumlahItem); // Ambil item unik

            foreach ($selectedItems as $itemId) {
                $jumlah = $faker->numberBetween(1, 100);
                $harga = Item::find($itemId)->harga_beli; // Ambil harga item dari database
                $totalHarga = $jumlah * $harga;

                PembelianDetail::create([
                    'pembelian_id' => $pembelianId,
                    'item_id' => $itemId,
                    'jumlah' => $jumlah,
                    'total_harga' => $totalHarga, // Simpan total harga langsung
                ]);
            }
        }
    }
}
