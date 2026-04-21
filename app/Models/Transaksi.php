<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'outlet_id', 'no_transaksi', 'tanggal', 'total', 'bayar', 'kembalian', 'metode_pembayaran'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
