<?php

namespace Database\Seeders;

use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Retail Customers',
                'description' => 'Regular retail customers who purchase at standard prices',
            ],
            [
                'name' => 'Wholesale Buyers',
                'description' => 'Business customers who purchase in bulk at wholesale prices',
            ],
            [
                'name' => 'Restaurant Partners',
                'description' => 'Restaurant businesses with special pricing and delivery arrangements',
            ],
            [
                'name' => 'VIP Members',
                'description' => 'Premium customers with special benefits and priority service',
            ],
            [
                'name' => 'Corporate Accounts',
                'description' => 'Business accounts with negotiated terms and pricing',
            ],
        ];

        foreach ($groups as $group) {
            CustomerGroup::create([
                'name' => $group['name'],
                'slug' => Str::slug($group['name']),
                'description' => $group['description'],
            ]);
        }
    }
} 