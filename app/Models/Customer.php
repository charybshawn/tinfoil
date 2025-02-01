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
        'address',
        'city',
        'state',
        'postal_code',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, 'group_id');
    }
} 