<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Project;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Get all project IDs to assign invoices to them
        $projectIds = Project::pluck('id')->toArray();

        foreach (range(1, 50) as $index) {
            Invoice::create([
                'project_id' => $faker->randomElement($projectIds),
                'title' => $faker->sentence,
                'notes' => $faker->paragraph,
                'total' => 0, // Placeholder, will be updated later
                'tax_percent' => 11,
                'issue_date' => $faker->date,
                'due_date' => $faker->date,
                'paid_date' => $faker->optional()->date,
            ]);
        }
    }
}
