<?php

namespace App\Services;

use Square\SquareClient;
use Square\Models\CreatePaymentRequest;
use Square\Models\Money;

class SquareService
{
    protected $client;
    protected $locationId;

    public function __construct()
    {
        $this->client = new SquareClient([
            'accessToken' => config('services.square.access_token'),
            'environment' => config('services.square.environment', 'sandbox'),
        ]);
        $this->locationId = config('services.square.location_id');
    }

    public function processPayment($sourceId, $amount, $currency = 'USD')
    {
        $money = new Money();
        $money->setAmount($amount * 100); // Convert to cents
        $money->setCurrency($currency);

        $createPaymentRequest = new CreatePaymentRequest($sourceId, uniqid('', true));
        $createPaymentRequest->setAmountMoney($money);
        $createPaymentRequest->setLocationId($this->locationId);

        $response = $this->client->getPaymentsApi()->createPayment($createPaymentRequest);

        if ($response->isSuccess()) {
            return $response->getResult();
        }

        throw new \Exception($response->getErrors()[0]->getDetail());
    }
} 