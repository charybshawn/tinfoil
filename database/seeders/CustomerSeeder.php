<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $retailGroup = CustomerGroup::where('name', 'Retail Customers')->first();
        $wholesaleGroup = CustomerGroup::where('name', 'Wholesale Buyers')->first();
        $restaurantGroup = CustomerGroup::where('name', 'Restaurant Partners')->first();

        $customers = [
            [
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'phone' => '555-0123',
                'address' => '123 Main St',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62701',
                'notes' => 'Regular customer, prefers morning deliveries',
                'group_id' => $retailGroup->id,
            ],
            [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '555-0124',
                'address' => '456 Oak Ave',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62702',
                'notes' => 'Wholesale customer',
                'group_id' => $wholesaleGroup->id,
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'phone' => '555-0125',
                'address' => '789 Pine St',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62703',
                'notes' => 'Allergic to shellfish',
                'group_id' => $retailGroup->id,
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'phone' => '555-0126',
                'address' => '321 Elm St',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62704',
                'group_id' => $retailGroup->id,
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'phone' => '555-0127',
                'address' => '654 Maple Dr',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62705',
                'notes' => 'Prefers text messages for communication',
                'group_id' => $restaurantGroup->id,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        // Create 5 additional random customers using the factory
        Customer::factory()->count(5)->create();
    }
} 