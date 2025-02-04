<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Get some existing invoices to create transactions for
        $invoices = Invoice::where('status', 'sent')->take(5)->get();

        foreach ($invoices as $invoice) {
            Transaction::factory()->create([
                'number' => 'TXN-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => $invoice->customer_id,
                'subtotal' => $invoice->subtotal,
                'tax' => $invoice->tax,
                'total' => $invoice->total,
                'transactionable_type' => Invoice::class,
                'transactionable_id' => $invoice->id,
                'created_at' => Carbon::now()->subMinutes(rand(1, 60)),
            ]);
        }
    }
} 