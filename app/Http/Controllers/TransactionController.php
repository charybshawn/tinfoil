<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function print(Transaction $transaction)
    {
        // Add your print logic here
        // For example, return a PDF view
        return view('transactions.print', compact('transaction'));
    }
} 