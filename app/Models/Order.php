<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'status',
        'subtotal',
        'tax',
        'total',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->number = static::generateOrderNumber();
        });
    }

    protected static function generateOrderNumber(): string
    {
        return DB::transaction(function () {
            $prefix = 'ORD';
            $yearMonth = now()->format('ym');
            
            // For SQLite, we'll just count all orders for this month
            $count = static::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();
            
            $sequence = $count + 1;
            
            return sprintf("%s/%s/%04d", $prefix, $yearMonth, $sequence);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
} 