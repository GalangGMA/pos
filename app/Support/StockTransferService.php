<?php

namespace App\Support;

use App\Models\Outlet;
use App\Models\ProductOutlet;
use App\Models\ProductStock;
use App\Models\Produk;
use App\Models\StockTransfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransferService
{
    public function transfer(int $tenantId, array $payload, ?User $actor = null): StockTransfer
    {
        return DB::transaction(function () use ($tenantId, $payload, $actor) {
            $produk = Produk::query()
                ->where('tenant_id', $tenantId)
                ->findOrFail($payload['produk_id']);

            $fromOutlet = Outlet::query()
                ->where('tenant_id', $tenantId)
                ->findOrFail($payload['from_outlet_id']);

            $toOutlet = Outlet::query()
                ->where('tenant_id', $tenantId)
                ->findOrFail($payload['to_outlet_id']);

            $quantity = (int) $payload['quantity'];

            if ($fromOutlet->id === $toOutlet->id) {
                throw ValidationException::withMessages([
                    'to_outlet_id' => ['Outlet tujuan harus berbeda dari outlet asal.'],
                ]);
            }

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => ['Jumlah transfer harus lebih dari 0.'],
                ]);
            }

            $sourceStock = ProductStock::query()
                ->where('tenant_id', $tenantId)
                ->where('produk_id', $produk->id)
                ->where('outlet_id', $fromOutlet->id)
                ->lockForUpdate()
                ->first();

            if (! $sourceStock || $sourceStock->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => ['Stok outlet asal tidak mencukupi untuk transfer ini.'],
                ]);
            }

            $targetStock = ProductStock::query()
                ->where('tenant_id', $tenantId)
                ->where('produk_id', $produk->id)
                ->where('outlet_id', $toOutlet->id)
                ->lockForUpdate()
                ->first();

            if (! $targetStock) {
                ProductOutlet::firstOrCreate([
                    'tenant_id' => $tenantId,
                    'produk_id' => $produk->id,
                    'outlet_id' => $toOutlet->id,
                ]);

                $targetStock = ProductStock::create([
                    'tenant_id' => $tenantId,
                    'produk_id' => $produk->id,
                    'outlet_id' => $toOutlet->id,
                    'stock' => 0,
                    'minimum_stock' => 0,
                ]);
            }

            $sourceStock->decrement('stock', $quantity);
            $targetStock->increment('stock', $quantity);

            $transfer = StockTransfer::create([
                'tenant_id' => $tenantId,
                'produk_id' => $produk->id,
                'from_outlet_id' => $fromOutlet->id,
                'to_outlet_id' => $toOutlet->id,
                'quantity' => $quantity,
                'status' => 'completed',
                'notes' => $payload['notes'] ?? null,
                'transferred_by' => $actor?->id,
                'completed_at' => now(),
            ]);

            $produk->updateQuietly([
                'stok' => ProductStock::query()
                    ->where('tenant_id', $tenantId)
                    ->where('produk_id', $produk->id)
                    ->sum('stock'),
            ]);

            return $transfer->fresh(['produk', 'fromOutlet', 'toOutlet', 'transferredBy']);
        });
    }
}
