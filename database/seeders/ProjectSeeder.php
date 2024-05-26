<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Client;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Get all client IDs to assign projects to them
        $startDate = $faker->dateTimeBetween('-1 year', 'now');

        // Generate due date after issue date
        $endDate = $faker->dateTimeBetween($startDate, '+1 year');

        $clientIds = Client::pluck('id')->toArray();

        foreach (range(1, 50) as $index) {
            Project::create([
                'name' => $faker->company,
                'client_id' => $faker->randomElement($clientIds),
                'notes' => $faker->optional()->paragraph,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }
    }
}
