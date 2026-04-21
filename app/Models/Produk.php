<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function kategori()
    {
        return $this->belongsTo(MKategori::class, 'kategori_id');
    }

    public function outletMappings()
    {
        return $this->hasMany(ProductOutlet::class, 'produk_id');
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'produk_id');
    }
}
