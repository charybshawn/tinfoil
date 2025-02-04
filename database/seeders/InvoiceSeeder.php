<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $variations = ProductVariation::with('product')->get();

        foreach ($customers->take(10) as $customer) {
            $invoice = Invoice::factory()->create([
                'customer_id' => $customer->id,
                'status' => 'sent',
            ]);

            // Add 1-3 items to each invoice
            $itemCount = rand(1, 3);
            for ($i = 0; $i < $itemCount; $i++) {
                $variation = $variations->random();
                $quantity = rand(1, 5);
                
                $invoice->items()->create([
                    'product_id' => $variation->product_id,
                    'product_variation_id' => $variation->id,
                    'description' => $variation->product->name . ' - ' . $variation->name,
                    'quantity' => $quantity,
                    'price' => $variation->retail_price,
                    'tax_rate' => 10,
                ]);
            }

            // Update invoice totals
            $invoice->updateTotals();
        }
    }
} 