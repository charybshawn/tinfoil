<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\SquareService;

class PaymentTest extends TestCase
{
    public function test_process_payment()
    {
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

        // Add assertions to verify the result of the processPayment call
    }
} 