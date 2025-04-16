<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Str;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        for ($i = 1; $i <= 50; $i++) {

            $tanggal = Carbon::now();
            $prefix = 'F' . $tanggal->format('Ym'); // contoh: F202504
            $randomCode = strtoupper(Str::random(6)); // contoh: 6 karakter acak
            $noFaktur = $prefix . '-' . $randomCode;

            Penjualan::create(
                [
                    'no_faktur' => $noFaktur,
                    'tanggal_penjualan' => $faker->dateTimeThisYear(),
                    'nama_pembeli' => $faker->name(),
                    'total_harga_akhir' => $faker->numberBetween(1000, 100000),
                    'total_item' => $faker->numberBetween(1, 100),
                    'total_uang' => $faker->numberBetween(1000, 100000),
                    'kembalian' => $faker->numberBetween(1000, 100000),
                    'metode' => $faker->randomElement(['CASH', 'KREDIT', 'TRANSFER', 'CEK']),
                    'status' => $faker->randomElement(['LUNAS', 'BELUM LUNAS']),
                ]
            );
        }
    }
}
