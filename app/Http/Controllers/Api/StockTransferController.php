<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesTenantContext;
use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Support\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StockTransferController extends Controller
{
    use ResolvesTenantContext;

    public function __construct(private StockTransferService $stockTransferService)
    {
    }

    public function index(Request $request)
    {
        $user = $this->currentUser($request);
        $tenant = $this->currentTenant($request);

        $query = StockTransfer::query()
            ->with(['produk', 'fromOutlet', 'toOutlet', 'transferredBy'])
            ->where('tenant_id', $tenant->id)
            ->latest('id');

        if ($user->role === 'kasir') {
            $query->where(function ($builder) use ($user) {
                $builder
                    ->where('from_outlet_id', $user->outlet_id)
                    ->orWhere('to_outlet_id', $user->outlet_id);
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $user = $this->currentUser($request);

        if (! in_array($user->role, ['owner', 'admin_toko'], true)) {
            throw ValidationException::withMessages([
                'transfer' => ['Hanya owner atau admin_toko yang bisa melakukan transfer stok.'],
            ]);
        }

        $tenant = $this->currentTenant($request);

        $validated = $request->validate([
            'produk_id' => 'required|integer|exists:produks,id',
            'from_outlet_id' => 'required|integer|exists:outlets,id',
            'to_outlet_id' => 'required|integer|exists:outlets,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $transfer = $this->stockTransferService->transfer($tenant->id, $validated, $user);

        return response()->json([
            'message' => 'Transfer stok berhasil disimpan.',
            'transfer' => $transfer,
        ], 201);
    }
}
