<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariationFactory extends Factory
{
    protected $model = ProductVariation::class;

    public function definition(): array
    {
        $retail_price = $this->faker->randomFloat(2, 10, 1000);
        
        return [
            'product_id' => Product::factory(),
            'name' => $this->faker->words(2, true),
            'upc' => $this->faker->ean13(),
            'unit_type' => $this->faker->randomElement(['kg', 'lb', 'unit']),
            'unit_value' => $this->faker->randomFloat(2, 0.1, 10),
            'weight_unit' => $this->faker->randomElement(['kg', 'lb']),
            'requires_date' => $this->faker->boolean(20),
            'retail_price' => $retail_price,
            'wholesale_price' => $retail_price * 0.7, // 30% discount for wholesale
            'status' => 'active',
        ];
    }
} 