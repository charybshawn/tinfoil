<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first() ?? Customer::factory()->create();
        $variation = ProductVariation::inRandomOrder()->first() ?? ProductVariation::factory()->create();
        
        return [
            'customer_id' => $customer->id,
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'subtotal' => $subtotal = fake()->randomFloat(2, 10, 1000),
            'tax' => $tax = $subtotal * 0.13,
            'total' => $subtotal + $tax,
            'shipping_address' => fake()->streetAddress(),
            'shipping_city' => fake()->city(),
            'shipping_state' => fake()->state(),
            'shipping_postal_code' => fake()->postcode(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $order->items()->create([
                'product_variation_id' => ProductVariation::inRandomOrder()->first()->id,
                'quantity' => fake()->numberBetween(1, 5),
                'price' => fake()->randomFloat(2, 10, 100),
                'unit_type' => 'item',
                'unit_value' => 1,
            ]);
        });
    }
} 