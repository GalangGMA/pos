<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MKategori extends Model
{
    use HasFactory;

    protected $table = 'm_kategoris';
    protected $fillable = ['tenant_id', 'nama'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function produks()
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
}
