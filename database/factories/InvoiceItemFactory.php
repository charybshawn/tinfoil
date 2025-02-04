<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $variation = ProductVariation::factory()->create();
        
        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => $variation->product_id,
            'product_variation_id' => $variation->id,
            'description' => $this->faker->sentence(),
            'quantity' => $this->faker->randomFloat(2, 1, 10),
            'price' => $variation->retail_price,
            'tax_rate' => 10,
        ];
    }
} 