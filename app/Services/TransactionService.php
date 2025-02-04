<?php

namespace App\Services;

use App\Models\Transaction;
use Square\SquareClient;

class TransactionService
{
    protected $square;
    
    public function __construct(SquareClient $square)
    {
        $this->square = $square;
    }

    public function createPosPayment(array $data)
    {
        // Create Square payment
        $payment = $this->square->getPaymentsApi()->createPayment([
            'sourceId' => $data['source_id'],
            'amountMoney' => [
                'amount' => $data['amount'] * 100,
                'currency' => 'USD'
            ],
            'locationId' => config('services.square.location_id')
        ]);

        // Create local transaction record
        return Transaction::create([
            'number' => $this->generateTransactionNumber(),
            'customer_id' => $data['customer_id'],
            'total' => $data['amount'],
            'square_payment_id' => $payment->getPayment()->getId(),
            'status' => 'completed'
        ]);
    }

    protected function generateTransactionNumber()
    {
        return 'TXN-' . date('Ymd') . '-' . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);
    }
} 