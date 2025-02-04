<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Truncate all tables
        DB::table('customer_groups')->truncate();
        DB::table('transactions')->truncate();
        DB::table('transaction_items')->truncate();
        // Add other tables you need to truncate

        // User::factory(10)->create();

        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create admin user first
        $user = User::firstOrCreate(
            ['email' => 'charybshawn@gmail.com'],
            [
                'name' => 'Shawn Harper',
                'password' => bcrypt('kngfqp57'),
                'email_verified_at' => now(),
            ]
        );

        // Run seeders
        $this->call([
            RoleSeeder::class,
            RoleAndPermissionSeeder::class,
            CategorySeeder::class,
            CustomerGroupSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            PaymentTermsSeeder::class,
            InvoiceSeeder::class,
            TransactionSeeder::class,
        ]);

        // Ensure the user has the admin role
        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
