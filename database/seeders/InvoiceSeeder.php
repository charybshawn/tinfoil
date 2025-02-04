<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\PaymentTerms;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $paymentTerms = PaymentTerms::all();
        $variations = ProductVariation::where('status', 'active')->get();

        foreach (range(1, 10) as $i) {
            $customer = $customers->random();
            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'payment_terms_id' => $paymentTerms->random()->id,
                'number' => sprintf('INV/%s/%04d', now()->format('ym'), $i),
                'title' => fake()->sentence(3),
                'message' => fake()->optional()->paragraph(),
                'issue_date' => now()->subDays(rand(1, 30)),
                'subtotal' => $subtotal = fake()->randomFloat(2, 100, 1000),
                'tax' => $tax = $subtotal * 0.1,
                'total' => $subtotal + $tax,
                'status' => fake()->randomElement(['draft', 'sent', 'paid', 'partial', 'overdue']),
                'is_recurring' => false,
            ]);

            // Create 1-5 invoice items
            $itemCount = rand(1, 5);
            for ($j = 0; $j < $itemCount; $j++) {
                $variation = $variations->random();
                $quantity = rand(1, 10);
                $price = $variation->retail_price;
                
                $invoice->items()->create([
                    'product_variation_id' => $variation->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'line_total' => $quantity * $price,
                    'unit_type' => $variation->unit_type,
                    'unit_value' => $variation->unit_value,
                    'weight_unit' => $variation->weight_unit,
                    'notes' => fake()->optional()->sentence(),
                ]);
            }
        }
    }
} 