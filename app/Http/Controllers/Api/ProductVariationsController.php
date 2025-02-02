<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductVariationsController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        return response()->json(
            $product->variations()
                ->select(['id', 'name', 'price', 'unit_type', 'unit_value'])
                ->where('status', 'active')
                ->get()
        );
    }
} 