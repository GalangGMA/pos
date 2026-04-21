<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOutlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'produk_id',
        'outlet_id',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
