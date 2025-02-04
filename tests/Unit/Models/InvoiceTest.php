<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\InvoiceItem;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Product;
use App\Models\ProductVariation;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_calculates_totals_correctly()
    {
        $invoice = Invoice::factory()->create();
        $product = Product::factory()->create();
        $variation = ProductVariation::factory()->create([
            'product_id' => $product->id,
            'retail_price' => 100,
        ]);
        
        // Create two items at $100 each plus 10% tax
        $invoice->items()->createMany([
            [
                'product_id' => $product->id,
                'product_variation_id' => $variation->id,
                'description' => 'Test Item 1',
                'quantity' => 1,
                'price' => $variation->retail_price,
                'tax_rate' => 10
            ],
            [
                'product_id' => $product->id,
                'product_variation_id' => $variation->id,
                'description' => 'Test Item 2',
                'quantity' => 1.5,
                'price' => $variation->retail_price,
                'tax_rate' => 10
            ]
        ]);

        $invoice->updateTotals();

        $this->assertEquals(250, $invoice->subtotal);
        $this->assertEquals(25, $invoice->tax);
        $this->assertEquals(275, $invoice->total);
    }

    #[Test]
    public function it_sends_email_to_customer()
    {
        Mail::fake();

        $customer = Customer::factory()->create([
            'email' => 'test@example.com',
            'secondary_emails' => ['second@example.com'],
        ]);

        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $invoice->sendEmail();

        Mail::assertSent(InvoiceMail::class, function ($mail) use ($invoice) {
            return $mail->invoice->id === $invoice->id;
        });

        $this->assertEquals('sent', $invoice->fresh()->status);
    }
} 