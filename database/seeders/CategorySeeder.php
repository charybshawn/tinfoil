<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Office essentials and stationery',
            ],
            [
                'name' => 'Software',
                'description' => 'Software licenses and subscriptions',
            ],
            [
                'name' => 'Services',
                'description' => 'Professional services and consulting',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']) . '-' . Str::random(6),
                'description' => $category['description'],
                'status' => 'active',
            ]);
        }

        // Create additional random categories if needed
        Category::factory()->count(5)->create();
    }
} 