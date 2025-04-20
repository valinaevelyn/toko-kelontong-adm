<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        for ($i = 1; $i <= 20; $i++) {

            // $harga_beli = $faker->numberBetween(1000, 100000);
            $harga_jual = $faker->numberBetween(1000, 100000);
            // Looping sampai harga jual > harga beli

            $stock_dus = $faker->numberBetween(1, 10); // Contoh jumlah stok dus
            $stock_rcg = $faker->numberBetween(1, 10); // Contoh jumlah stok renceng
            $stock_pcs = $faker->numberBetween(1, 100); // Contoh jumlah stok pcs
            $dus_in_pcs = $faker->numberBetween(10, 20); // Jumlah pcs per dus
            $rcg_in_pcs = $faker->numberBetween(5, 10); // Jumlah pcs per renceng

            Item::create([
                'nama' => $faker->word(),
                'merek' => $faker->company,
                // 'harga_beli' => $harga_beli,
                'harga_jual' => $harga_jual,
                'stock_dus' => $stock_dus,
                'stock_rcg' => $stock_rcg,
                'stock_pcs' => $stock_pcs,
                'dus_in_pcs' => $dus_in_pcs,
                'rcg_in_pcs' => $rcg_in_pcs,
            ]);
        }

    }
}
