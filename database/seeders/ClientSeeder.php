<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {
            Client::create([
                'name' => $faker->company,
                'description' => $faker->optional()->sentence,
                'phone' => $faker->phoneNumber,
                'email' => $faker->email,
                'address' => $faker->address,
            ]);
        }
    }
}
