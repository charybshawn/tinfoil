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
                'name' => 'Fresh Fish',
                'description' => 'Fresh fish sourced daily from local suppliers',
            ],
            [
                'name' => 'Shellfish',
                'description' => 'Premium quality shellfish and crustaceans',
            ],
            [
                'name' => 'Specialty Items',
                'description' => 'Unique and specialty seafood products',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
            ]);
        }
    }
} 