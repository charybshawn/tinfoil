<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_variations';

    protected $fillable = [
        'name',
        'upc',
        'unit_type',
        'unit_value',
        'requires_date',
        'retail_price',
        'wholesale_price',
        'status',
        'options',
    ];

    protected $casts = [
        'requires_date' => 'boolean',
        'retail_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'options' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
} 