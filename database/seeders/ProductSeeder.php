<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Variation;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 products, each with 3 variations
        Product::factory()
            ->count(5)
            ->create()
            ->each(function ($product) {
                $product->variations()->saveMany(
                    Variation::factory()->count(3)->make()
                );
            });
    }
} 