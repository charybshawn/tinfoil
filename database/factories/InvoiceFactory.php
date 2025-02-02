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
        $customer = Customer::inRandomOrder()->first();
        $subtotal = fake()->randomFloat(2, 100, 1000);
        $tax = $subtotal * 0.1; // 10% tax
        
        return [
            'customer_id' => $customer->id,
            'payment_terms_id' => PaymentTerms::inRandomOrder()->first()->id,
            'issue_date' => $date = fake()->dateTimeBetween('-30 days', 'now'),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'status' => fake()->randomElement(['draft', 'sent', 'partial', 'paid', 'overdue']),
            'is_recurring' => fake()->boolean(20), // 20% chance of being recurring
            'recurring_frequency' => fn (array $attrs) => $attrs['is_recurring'] ? fake()->randomElement(['weekly', 'monthly', 'quarterly']) : null,
            'next_invoice_date' => fn (array $attrs) => $attrs['is_recurring'] ? Carbon::parse($date)->addMonth() : null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
} 