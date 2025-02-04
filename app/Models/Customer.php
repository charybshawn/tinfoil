<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'street_address',
        'city',
        'prov',
        'country',
        'postal_code',
        'secondary_emails',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'secondary_emails' => 'array',
    ];

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, 'group_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
} 