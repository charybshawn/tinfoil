<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 random orders
        Order::factory()
            ->count(20)
            ->create();
    }
} 