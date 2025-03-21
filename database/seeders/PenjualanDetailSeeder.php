<?php

namespace Database\Seeders;

use App\Models\PenjualanDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        for ($i = 1; $i <= 20; $i++) {
            PenjualanDetail::create(
                [
                    'penjualan_id' => $faker->numberBetween(1, 20),
                    'item_id' => $faker->numberBetween(1, 20),
                    'jumlah' => $faker->numberBetween(1, 100),
                ]
            );
        }
    }
}
