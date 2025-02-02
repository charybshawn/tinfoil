<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'customer_id',
        'payment_terms_id',
        'issue_date',
        'due_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
        'is_recurring',
        'recurring_frequency',
        'next_invoice_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'next_invoice_date' => 'date',
        'is_recurring' => 'boolean',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentTerms(): BelongsTo
    {
        return $this->belongsTo(PaymentTerms::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function parentInvoice()
    {
        return $this->belongsTo(Invoice::class, 'parent_invoice_id');
    }

    public function childInvoices()
    {
        return $this->hasMany(Invoice::class, 'parent_invoice_id');
    }

    public function getDueDateAttribute()
    {
        return $this->issue_date->addDays($this->paymentTerms->days);
    }

    protected static function generateInvoiceNumber(): string
    {
        return DB::transaction(function () {
            $prefix = 'INV';
            $yearMonth = now()->format('ym');
            
            // For SQLite, we'll just count all invoices for this month
            $count = static::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();
            
            $sequence = $count + 1;
            
            return sprintf("%s/%s/%04d", $prefix, $yearMonth, $sequence);
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $invoice->number = static::generateInvoiceNumber();
        });
    }
} 