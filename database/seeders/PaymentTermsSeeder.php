<?php

namespace Database\Seeders;

use App\Models\PaymentTerms;
use Illuminate\Database\Seeder;

class PaymentTermsSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            [
                'name' => 'Due on Receipt',
                'days' => 0,
                'description' => 'Payment is due immediately upon receipt of invoice',
            ],
            [
                'name' => 'Net 15',
                'days' => 15,
                'description' => 'Payment is due within 15 days',
            ],
            [
                'name' => 'Net 30',
                'days' => 30,
                'description' => 'Payment is due within 30 days',
            ],
            [
                'name' => 'Net 45',
                'days' => 45,
                'description' => 'Payment is due within 45 days',
            ],
            [
                'name' => 'Net 60',
                'days' => 60,
                'description' => 'Payment is due within 60 days',
            ],
            [
                'name' => '2/10 Net 30',
                'days' => 30,
                'description' => '2% discount if paid within 10 days, otherwise full amount due in 30 days',
            ],
        ];

        foreach ($terms as $term) {
            PaymentTerms::create($term);
        }
    }
} 