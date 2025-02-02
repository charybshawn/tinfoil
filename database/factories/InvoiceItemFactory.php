<?php

namespace Database\Factories;

use App\Models\InvoiceItem;
use App\Models\Variation;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $variation = Variation::inRandomOrder()->first();
        
        return [
            'variation_id' => $variation->id,
            'quantity' => fake()->numberBetween(1, 20),
            'price' => $variation->wholesale_price ?? $variation->retail_price,
            'unit_type' => $variation->unit_type,
            'unit_value' => $variation->unit_value,
            'weight_unit' => $variation->weight_unit,
            'is_recurring' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }
} 