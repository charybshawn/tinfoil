<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\SquareService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function print(Invoice $invoice)
    {
        return view('invoices.print', [
            'invoice' => $invoice->load([
                'customer', 
                'items.productVariation.product',
                'paymentTerms'
            ])
        ]);
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function showPaymentPage(Invoice $invoice)
    {
        return view('invoices.pay', [
            'invoice' => $invoice,
            'squareAppId' => config('services.square.app_id'),
            'squareLocationId' => config('services.square.location_id'),
        ]);
    }

    public function processPayment(Request $request, Invoice $invoice, SquareService $square)
    {
        $request->validate([
            'sourceId' => 'required|string',
        ]);

        try {
            $payment = $square->processPayment(
                $request->sourceId,
                $invoice->total
            );
            
            $invoice->markAsPaid();
            
            return response()->json([
                'success' => true,
                'payment' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }
} 