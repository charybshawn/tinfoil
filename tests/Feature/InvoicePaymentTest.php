<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\SquareService;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create and authenticate admin user with permissions
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'admin']);
        $permissions = [
            'process_payments',
            'view_invoices',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $role->givePermissionTo($permission);
        }
        
        $user->assignRole('admin');
        $this->actingAs($user);
    }

    #[Test]
    public function customer_can_view_payment_page()
    {
        $invoice = Invoice::factory()->create([
            'total' => 100.00,
            'status' => 'sent',
        ]);

        $response = $this->get(route('invoice.pay', $invoice));
        $response->assertSuccessful();
    }

    #[Test]
    public function can_process_successful_payment()
    {
        $invoice = Invoice::factory()->create([
            'status' => 'sent',
        ]);

        $this->mock(SquareService::class, function ($mock) {
            $mock->shouldReceive('processPayment')
                ->once()
                ->andReturn([
                    'payment' => [
                        'id' => 'test_payment_id',
                        'status' => 'COMPLETED',
                    ]
                ]);
        });

        $response = $this->postJson(route('invoice.process-payment', $invoice), [
            'sourceId' => 'cnon:card-nonce-ok',
        ]);

        $response->assertSuccessful();
        $this->assertEquals('paid', $invoice->fresh()->status);
    }
} 