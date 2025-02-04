<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Create a retail group
        $retailGroup = CustomerGroup::create([
            'name' => 'Retail Customers',
            'slug' => 'retail',
            'description' => 'Regular retail customers',
        ]);

        // Create a wholesale group
        $wholesaleGroup = CustomerGroup::create([
            'name' => 'Wholesale Customers',
            'slug' => 'wholesale',
            'description' => 'Wholesale and bulk buyers',
        ]);

        // Create some retail customers
        Customer::factory()
            ->count(10)
            ->state(['group_id' => $retailGroup->id])
            ->create();

        // Create some wholesale customers with secondary emails
        Customer::factory()
            ->count(5)
            ->state([
                'group_id' => $wholesaleGroup->id,
                'secondary_emails' => [
                    'accounting@example.com',
                    'purchasing@example.com'
                ]
            ])
            ->create();
    }
} 