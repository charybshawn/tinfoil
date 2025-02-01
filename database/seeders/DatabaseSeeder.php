<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleAndPermissionSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerGroupSeeder::class,
            CustomerSeeder::class,
        ]);

        // Create or update admin user
        $user = User::updateOrCreate(
            ['email' => 'charybshawn@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('kngfqp57'),
                'email_verified_at' => now(),
            ]
        );
        
        // Assign admin role
        $user->assignRole('admin');
    }
}
