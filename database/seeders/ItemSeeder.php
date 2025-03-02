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
            Item::create(
                [
                    'nama' => $faker->word(),
                    'merek' => $faker->company,
                    'uom' => $faker->randomElement(['pcs', 'kg', 'm']),
                    'harga' => $faker->numberBetween(1000, 100000),
                    'stock' => $faker->numberBetween(1, 100),
                ]
            );
        }
    }
}
