<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Variation;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first();
        $subtotal = fake()->randomFloat(2, 50, 500);
        
        return [
            'customer_id' => $customer->id,
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
            'shipping_address' => $customer->address,
            'shipping_city' => $customer->city,
            'shipping_state' => $customer->state,
            'shipping_postal_code' => $customer->postal_code,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            // Make sure we have variations before trying to create items
            if (Variation::count() > 0) {
                // Create 1-5 order items
                $itemCount = rand(1, 5);
                
                for ($i = 0; $i < $itemCount; $i++) {
                    if ($variation = Variation::inRandomOrder()->first()) {
                        $order->items()->create([
                            'variation_id' => $variation->id,
                            'quantity' => rand(1, 10),
                            'price' => $variation->retail_price,
                            'unit_type' => $variation->unit_type,
                            'unit_value' => $variation->unit_value,
                            'weight_unit' => $variation->weight_unit,
                        ]);
                    }
                }

                // Create an initial status
                $order->statuses()->create([
                    'status' => $order->status,
                    'notes' => 'Order created',
                ]);

                // Create a payment record
                $order->payments()->create([
                    'amount' => $order->total,
                    'status' => $order->status === 'completed' ? 'completed' : 'pending',
                    'stripe_payment_intent_id' => 'pi_' . fake()->regexify('[A-Za-z0-9]{24}'),
                    'stripe_payment_method_id' => 'pm_' . fake()->regexify('[A-Za-z0-9]{24}'),
                ]);
            }
        });
    }
} 