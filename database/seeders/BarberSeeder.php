<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarberSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $tenantId = 1;
        $outletId = 1;

        $menus = [
            [
                'nama' => 'Hair Cut',
                'kategori' => 'Barber',
                'image' => 'produk/barber/haircut.jpg',
                'harga' => 50000,
                'stok' => 999,
            ],
            [
                'nama' => 'Kids Hair Cut',
                'kategori' => 'Barber',
                'image' => 'produk/barber/kids-haircut.jpg',
                'harga' => 60000,
                'stok' => 999,
            ],
            [
                'nama' => 'Gentlemens Grooming',
                'kategori' => 'Barber',
                'image' => 'produk/barber/grooming.jpg',
                'harga' => 80000,
                'stok' => 999,
            ],
            [
                'nama' => 'Gentlemens Grooming & Massage',
                'kategori' => 'Barber',
                'image' => 'produk/barber/grooming-massage.jpg',
                'harga' => 120000,
                'stok' => 999,
            ],
            [
                'nama' => 'Wash & Hair Spa',
                'kategori' => 'Barber',
                'image' => 'produk/barber/hairspa.jpg',
                'harga' => 100000,
                'stok' => 999,
            ],
            [
                'nama' => 'Shaving',
                'kategori' => 'Barber',
                'image' => 'produk/barber/shaving.jpg',
                'harga' => 40000,
                'stok' => 999,
            ],
            [
                'nama' => 'Black Hair Colour',
                'kategori' => 'Barber',
                'image' => 'produk/barber/black-colour.jpg',
                'harga' => 150000,
                'stok' => 999,
            ],
            [
                'nama' => 'Fashion Hair Colour',
                'kategori' => 'Barber',
                'image' => 'produk/barber/fashion-colour.jpg',
                'harga' => 500000,
                'stok' => 999,
            ],
            [
                'nama' => 'Hair Perm (Curly)',
                'kategori' => 'Barber',
                'image' => 'produk/barber/perm-curly.jpg',
                'harga' => 500000,
                'stok' => 999,
            ],
            [
                'nama' => 'Hair Down Perm',
                'kategori' => 'Barber',
                'image' => 'produk/barber/down-perm.jpg',
                'harga' => 300000,
                'stok' => 999,
            ],
        ];

        foreach ($menus as $menu) {

            $produkId = DB::table('produks')->insertGetId([
                'tenant_id'  => $tenantId,
                'nama'       => $menu['nama'],
                'kategori'   => $menu['kategori'],
                'image'      => $menu['image'],
                'harga'      => $menu['harga'],
                'stok'       => $menu['stok'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('product_stocks')->insert([
                'tenant_id'     => $tenantId,
                'produk_id'     => $produkId,
                'outlet_id'     => $outletId,
                'stock'         => $menu['stok'],
                'minimum_stock' => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);

            DB::table('product_outlets')->insert([
                'tenant_id'  => $tenantId,
                'produk_id'  => $produkId,
                'outlet_id'  => $outletId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
