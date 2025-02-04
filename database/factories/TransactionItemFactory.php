<?php

namespace Database\Factories;

use App\Models\ProductVariation;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionItemFactory extends Factory
{
    protected $model = TransactionItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 10);
        $price = $this->faker->randomFloat(2, 5, 100);
        $tax = ($quantity * $price) * 0.1; // 10% tax
        $total = ($quantity * $price) + $tax;

        return [
            'transaction_id' => Transaction::factory(),
            'product_variation_id' => ProductVariation::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'tax' => $tax,
            'total' => $total,
            'square_item_data' => [
                'catalog_object_id' => $this->faker->uuid(),
                'item_type' => 'ITEM',
                'quantity' => $quantity,
            ],
        ];
    }
} 