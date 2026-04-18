<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id', 'produk_id', 'jumlah', 'harga', 'subtotal'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}

