<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        Invoice::factory()
            ->count(20)
            ->create()
            ->each(function ($invoice) {
                // Create 1-5 items per invoice
                $invoice->items()->saveMany(
                    \App\Models\InvoiceItem::factory()
                        ->count(fake()->numberBetween(1, 5))
                        ->make(['invoice_id' => $invoice->id])
                );

                // Create 0-2 payments per invoice
                if ($invoice->status !== 'draft') {
                    $invoice->payments()->saveMany(
                        \App\Models\InvoicePayment::factory()
                            ->count(fake()->numberBetween(0, 2))
                            ->make(['invoice_id' => $invoice->id])
                    );
                }
            });
    }
} 