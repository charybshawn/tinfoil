<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // Create permissions
        $permissions = [
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',
            // Add other permissions as needed
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Sync all permissions to admin role
        $adminRole->syncPermissions(Permission::all());
        
        // Sync specific permissions to employee role
        $employeeRole->syncPermissions([
            'view_invoices',
            'create_invoices',
            'edit_invoices',
        ]);
    }
} 