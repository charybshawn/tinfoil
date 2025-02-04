<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 10, 1000);
        $tax = $total * 0.1; // 10% tax
        
        return [
            'number' => 'TXN-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'subtotal' => $total,
            'tax' => $tax,
            'total' => $total + $tax,
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'square_payment_id' => $this->faker->uuid(),
            'square_order_id' => $this->faker->uuid(),
            'square_data' => json_encode([
                'payment_id' => $this->faker->uuid(),
                'order_id' => $this->faker->uuid(),
                'receipt_number' => $this->faker->numberBetween(10000000, 99999999),
            ]),
            'location_id' => $this->faker->uuid(),
            'device_id' => $this->faker->uuid(),
            'transactionable_type' => Invoice::class,
            'transactionable_id' => Invoice::factory(),
            'created_at' => Carbon::now()->subMinutes(rand(1, 60)),
        ];
    }

    public function completed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }
} 