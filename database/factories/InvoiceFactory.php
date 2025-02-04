<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PaymentTerms;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first() ?? Customer::factory()->create();
        $date = $this->faker->dateTimeBetween('-30 days', 'now');
        $subtotal = $this->faker->randomFloat(2, 100, 1000);
        $tax = $subtotal * 0.1; // 10% tax

        return [
            'customer_id' => Customer::factory(),
            'payment_terms_id' => PaymentTerms::factory(),
            'number' => 'INV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'issue_date' => now(),
            'status' => 'draft',
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'title' => $this->faker->optional()->sentence(3),
            'message' => $this->faker->optional()->paragraph(),
            'is_recurring' => $isRecurring = $this->faker->boolean(20), // 20% chance of being recurring
            'recurring_frequency' => fn (array $attrs) => $attrs['is_recurring'] ? 
                $this->faker->randomElement(['weekly', 'monthly', 'quarterly']) : null,
            'next_invoice_date' => fn (array $attrs) => $attrs['is_recurring'] ? 
                Carbon::parse($date)->addMonth() : null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
} 