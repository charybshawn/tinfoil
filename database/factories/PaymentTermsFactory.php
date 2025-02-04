<?php

namespace Database\Factories;

use App\Models\PaymentTerms;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentTermsFactory extends Factory
{
    protected $model = PaymentTerms::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word . ' Terms',
            'days' => $this->faker->randomElement([0, 7, 14, 30, 45, 60]),
            'description' => $this->faker->sentence(),
        ];
    }
} 