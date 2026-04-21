<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'price',
        'max_outlets',
        'max_users',
        'max_products',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];
}
