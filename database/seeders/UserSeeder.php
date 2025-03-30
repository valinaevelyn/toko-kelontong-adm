<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        User::create([
            'name' => 'User 1',
            'email' => 'user@gmail.com',
            'password' => 'password'
        ]);

        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => 'password'
            ]);
        }
    }
}
