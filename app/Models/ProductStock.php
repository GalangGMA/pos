<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'produk_id',
        'outlet_id',
        'stock',
        'minimum_stock',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
