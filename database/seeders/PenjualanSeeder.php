<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        for ($i = 1; $i <= 20; $i++) {
            Penjualan::create(
                [
                    'tanggal_penjualan' => $faker->dateTimeThisYear(),
                    'nama_pembeli' => $faker->name(),
                    'total_harga' => $faker->numberBetween(1000, 100000),
                    'total_item' => $faker->numberBetween(1, 100),
                    'total_uang' => $faker->numberBetween(1000, 100000),
                    'kembalian' => $faker->numberBetween(1000, 100000),
                    'metode' => $faker->randomElement(['cash', 'kredit']),
                    'status' => $faker->randomElement(['lunas', 'belum lunas']),
                ]
            );
        }
    }
}
