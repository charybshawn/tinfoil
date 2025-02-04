<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Services\SquareService;

class TestInvoiceFlow extends Command
{
    protected $signature = 'test:invoice-flow {invoice}';
    protected $description = 'Test the invoice creation and payment flow';

    public function handle(SquareService $square)
    {
        $invoice = Invoice::findOrFail($this->argument('invoice'));
        
        if ($this->confirm('Send test email?')) {
            $invoice->sendEmail();
            $this->info('Email sent to Mailtrap!');
        }

        if ($this->confirm('Test Square payment?')) {
            try {
                $payment = $square->processPayment(
                    'cnon:card-nonce-ok', // Test nonce
                    $invoice->total
                );
                
                $this->info('Test payment successful!');
                $this->line("Payment ID: {$payment['payment']['id']}");
            } catch (\Exception $e) {
                $this->error("Payment failed: {$e->getMessage()}");
            }
        }
    }
} 