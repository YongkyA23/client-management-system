<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InvoiceDetail;
use App\Models\Invoice;
use App\Models\ServiceCategory;
use Faker\Factory as Faker;

class InvoiceDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();
        $invoiceIds = Invoice::pluck('id')->toArray();
        $serviceCategoryIds = ServiceCategory::pluck('id')->toArray();
        // Update the total of the invoice


        foreach ($invoiceIds as $invoiceId) {
            $total = 0;

            foreach (range(1, $faker->numberBetween(1, 10)) as $index) {
                $quantity = $faker->numberBetween(1, 10);
                $price = $faker->numberBetween(100, 1000);
                $totalPrice = $quantity * $price;

                InvoiceDetail::create([
                    'invoice_id' => $invoiceId,
                    'service_category_id' => $faker->randomElement($serviceCategoryIds),
                    'name' => $faker->word,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                ]);

                $total += $totalPrice;
            }
            $invoice = Invoice::find($invoiceId);
            $taxPercent = $invoice->tax_percent;

            // Calculate the tax amount
            $taxAmount = $total * $taxPercent / 100;

            // Calculate the total after tax
            $totalAfterTax = $total + $taxAmount;

            $invoice->total = $totalAfterTax;

            $invoice->save();
        }
    }
}
