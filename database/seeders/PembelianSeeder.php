<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Pembelian;

class PembelianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        for ($i = 1; $i <= 20; $i++) {
            Pembelian::create(
                [
                    'tanggal_pembelian' => $faker->dateTimeThisYear(),
                    'nama_supplier' => $faker->company(),
                    'total_harga' => $faker->numberBetween(1000, 100000),
                    'total_item' => $faker->numberBetween(1, 100),
                    'total_uang' => $faker->numberBetween(1000, 100000),
                    'kembalian' => $faker->numberBetween(1000, 100000),
                    'metode' => $faker->randomElement(['CASH', 'KREDIT']),
                    'status' => $faker->randomElement(['LUNAS', 'BELUM LUNAS']),
                ]
            );
        }
    }
}
