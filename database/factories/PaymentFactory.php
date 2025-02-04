<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $payableTypes = [
            Order::class,
            Invoice::class,
        ];
        
        $payableType = fake()->randomElement($payableTypes);
        $payable = $payableType::factory();

        return [
            'payable_type' => $payableType,
            'payable_id' => $payable,
            'amount' => fake()->randomFloat(2, 10, 1000),
            'method' => fake()->randomElement(['bank_transfer', 'check', 'cash', 'stripe']),
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'stripe_payment_intent_id' => fn (array $attributes) => 
                $attributes['method'] === 'stripe' ? 'pi_' . fake()->regexify('[A-Za-z0-9]{24}') : null,
            'stripe_payment_method_id' => fn (array $attributes) => 
                $attributes['method'] === 'stripe' ? 'pm_' . fake()->regexify('[A-Za-z0-9]{24}') : null,
            'reference' => fn (array $attributes) => match($attributes['method']) {
                'bank_transfer' => fake()->regexify('[A-Z0-9]{12}'),
                'check' => fake()->regexify('[0-9]{6}'),
                default => null,
            },
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Configure the payment for an order
     */
    public function forOrder(Order $order): static
    {
        return $this->state(fn (array $attributes) => [
            'payable_type' => Order::class,
            'payable_id' => $order->id,
            'amount' => $order->total,
        ]);
    }

    /**
     * Configure the payment for an invoice
     */
    public function forInvoice(Invoice $invoice): static
    {
        return $this->state(fn (array $attributes) => [
            'payable_type' => Invoice::class,
            'payable_id' => $invoice->id,
            'amount' => $invoice->total,
        ]);
    }

    /**
     * Mark the payment as completed
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed'
        ]);
    }

    /**
     * Configure as a Stripe payment
     */
    public function stripe(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'stripe',
            'stripe_payment_intent_id' => 'pi_' . fake()->regexify('[A-Za-z0-9]{24}'),
            'stripe_payment_method_id' => 'pm_' . fake()->regexify('[A-Za-z0-9]{24}'),
        ]);
    }
} 