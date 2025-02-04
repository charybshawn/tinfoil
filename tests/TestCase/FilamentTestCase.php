<?php

namespace Tests\TestCase;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FilamentTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role with all permissions
        $role = Role::firstOrCreate(['name' => 'admin']);
        $permissions = [
            'view_any_invoice',
            'view_invoice',
            'create_invoice',
            'update_invoice',
            'delete_invoice',
            'manage_settings',
            'process_payments',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $role->givePermissionTo($permission);
        }

        // Create and authenticate admin user
        $user = User::factory()->create(['email' => 'admin@example.com']);
        $user->assignRole('admin');
        $this->actingAs($user);
    }

    protected function getResourceClass()
    {
        throw new \Exception('Please implement getResourceClass() in your test');
    }

    protected function getResourceRoute(): string
    {
        $resource = $this->getResourceClass();
        return str_replace('\\', '/', $resource::getUrl('index'));
    }
} 