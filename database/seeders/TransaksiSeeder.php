<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan minimal ada produk agar detail transaksi bisa dibuat
        if (Produk::count() == 0) {
            Produk::insert([
                ['nama' => 'Kopi Latte', 'harga' => 25000, 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'Americano', 'harga' => 22000, 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'Cappuccino', 'harga' => 27000, 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'Matcha Latte', 'harga' => 30000, 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'Mochaccino', 'harga' => 29000, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $produkList = Produk::all();

        // Generate transaksi selama 3 bulan terakhir
        for ($i = 0; $i < 120; $i++) {
            $tanggal = Carbon::now()->subDays(rand(0, 90))->setTime(rand(8, 20), rand(0, 59));

            $transaksi = Transaksi::create([
                'no_transaksi' => 'TRX-' . strtoupper(uniqid()),
                'total' => 0,
                'bayar' => 0,
                'kembalian' => 0,
                'tanggal' => $tanggal,
                'created_at' => $tanggal,
                'updated_at' => $tanggal,
            ]);

            // Random item dalam transaksi
            $jumlahProduk = rand(1, 5);
            $total = 0;

            for ($j = 0; $j < $jumlahProduk; $j++) {
                $produk = $produkList->random();
                $jumlah = rand(1, 3);
                $subtotal = $produk->harga * $jumlah;
                $total += $subtotal;

                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $produk->id,
                    'jumlah' => $jumlah,
                    'harga' => $produk->harga,
                    'subtotal' => $subtotal,
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal,
                ]);
            }

            $transaksi->update([
                'total' => $total,
                'bayar' => $total,
                'kembalian' => 0,
            ]);
        }
    }
}
