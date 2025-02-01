<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Variation;
use Illuminate\Database\Eloquent\Factories\Factory;

class VariationFactory extends Factory
{
    protected $model = Variation::class;

    public function definition(): array
    {
        $unitType = fake()->randomElement(['item', 'weight']);
        
        return [
            'name' => fake()->words(2, true),
            'upc' => fake()->numerify('78#######'),
            'unit_type' => $unitType,
            'unit_value' => $unitType === 'weight' ? fake()->numberBetween(100, 2000) : fake()->numberBetween(1, 100),
            'weight_unit' => $unitType === 'weight' ? 'g' : null,
            'retail_price' => fake()->randomFloat(2, 5, 100),
            'requires_date' => fake()->boolean(20),
            'status' => 'active',
        ];
    }
} 