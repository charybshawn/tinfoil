<?php

namespace Database\Factories;

use App\Models\InvoicePayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoicePaymentFactory extends Factory
{
    protected $model = InvoicePayment::class;

    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 50, 500),
            'method' => fake()->randomElement(['bank_transfer', 'check', 'cash', 'stripe']),
            'reference_number' => fake()->optional()->regexify('[A-Z0-9]{10}'),
            'payment_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
} 