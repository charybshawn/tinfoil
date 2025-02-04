<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\PaymentTerms;
use App\Models\User;
use Tests\TestCase\FilamentTestCase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class InvoiceResourceTest extends FilamentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role with permissions
        $role = Role::firstOrCreate(['name' => 'admin']);
        $permissions = [
            'view_any_invoice',
            'view_invoice',
            'create_invoice',
            'update_invoice',
            'delete_invoice',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $role->givePermissionTo($permission);
        }

        // Create and authenticate admin user
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
    }

    protected function getResourceClass()
    {
        return InvoiceResource::class;
    }

    #[Test]
    public function can_view_invoice_list()
    {
        Invoice::factory()->count(3)->create();

        $response = $this->get($this->getResourceRoute());

        $response->assertSuccessful();
        $response->assertSee('Invoices');
    }

    #[Test]
    public function can_create_invoice()
    {
        $customer = Customer::factory()->create();
        $paymentTerms = PaymentTerms::factory()->create();

        $response = $this->get(InvoiceResource::getUrl('create'));
        $response->assertSuccessful();

        $response = $this->post(InvoiceResource::getUrl('create'), [
            'data' => [
                'customer_id' => $customer->id,
                'payment_terms_id' => $paymentTerms->id,
                'issue_date' => now()->format('Y-m-d'),
                'status' => 'draft',
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'customer_id' => $customer->id,
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function can_edit_invoice()
    {
        $invoice = Invoice::factory()->create();

        $response = $this->get(InvoiceResource::getUrl('edit', [
            'record' => $invoice,
        ]));
        $response->assertSuccessful();

        $response = $this->patch(InvoiceResource::getUrl('edit', [
            'record' => $invoice,
        ]), [
            'data' => [
                'status' => 'sent',
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'sent',
        ]);
    }
} 