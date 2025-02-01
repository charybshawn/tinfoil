<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public static function rules($ignoreId = null)
    {
        return [
            'name' => [
                'required',
                Rule::unique('categories', 'name')
                    ->whereNull('deleted_at')
                    ->ignoreCase()
                    ->ignore($ignoreId),
            ],
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
} 