<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ResolvesTenantContext;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\ProductOutlet;
use App\Models\ProductStock;
use App\Models\Produk;
use App\Support\TenantPlanGate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class ProdukController extends Controller
{
    use ResolvesTenantContext;

    public function __construct(private TenantPlanGate $tenantPlanGate)
    {
    }

    public function index()
    {
        $request = request();
        $tenant = $this->currentTenant($request);
        $outlet = $this->currentOutlet($request);

        $produk = Produk::query()
            ->where('tenant_id', $tenant->id)
            ->whereHas('outletMappings', fn ($query) => $query->where('outlet_id', $outlet->id))
            ->with(['stocks' => fn ($query) => $query->where('outlet_id', $outlet->id)])
            ->orderByDesc('created_at')
            ->get();

        $produk->transform(function ($item) use ($outlet) {
            $stock = $item->stocks->firstWhere('outlet_id', $outlet->id);
            $item->stok = (int) ($stock->stock ?? 0);
            $item->image_url = $item->image ? URL::to('storage/' . $item->image) : null;
            unset($item->stocks);

            return $item;
        });

        return response()->json($produk);
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->currentTenant($request);
        $outlet = $this->currentOutlet($request);
        $produk = Produk::query()
            ->where('tenant_id', $tenant->id)
            ->whereKey($id)
            ->whereHas('outletMappings', fn ($query) => $query->where('outlet_id', $outlet->id))
            ->with(['stocks' => fn ($query) => $query->where('outlet_id', $outlet->id)])
            ->first();

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $produk->stok = (int) optional($produk->stocks->first())->stock;
        $produk->image_url = $produk->image ? URL::to('storage/' . $produk->image) : null;

        return response()->json($produk);
    }

    public function store(Request $request)
    {
        $tenant = $this->currentTenant($request);
        $currentOutlet = $this->currentOutlet($request);
        $this->tenantPlanGate->assertCanCreateProduct($tenant->id);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'gambar' => 'nullable|image|max:2048',
            'outlet_ids' => 'nullable|array',
            'outlet_ids.*' => 'integer|exists:outlets,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['nama', 'harga', 'kategori']);
        $data['tenant_id'] = $tenant->id;

        if ($request->hasFile('gambar')) {
            $data['image'] = $request->file('gambar')->store('produk-images', 'public');
        }

        $produk = Produk::create($data);

        $outletIds = collect($request->input('outlet_ids', [$currentOutlet->id]))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $validOutletIds = Outlet::query()
            ->where('tenant_id', $tenant->id)
            ->whereIn('id', $outletIds)
            ->pluck('id');

        foreach ($validOutletIds as $outletId) {
            ProductOutlet::create([
                'tenant_id' => $tenant->id,
                'produk_id' => $produk->id,
                'outlet_id' => $outletId,
            ]);

            ProductStock::create([
                'tenant_id' => $tenant->id,
                'produk_id' => $produk->id,
                'outlet_id' => $outletId,
                'stock' => $outletId === $currentOutlet->id ? (int) $request->stok : 0,
                'minimum_stock' => 0,
            ]);
        }

        return response()->json($produk, 201);
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->currentTenant($request);
        $currentOutlet = $this->currentOutlet($request);
        $produk = Produk::query()->where('tenant_id', $tenant->id)->find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|string|max:255',
            'harga' => 'sometimes|numeric',
            'stok' => 'sometimes|integer',
            'gambar' => 'nullable|image|max:2048',
            'outlet_ids' => 'nullable|array',
            'outlet_ids.*' => 'integer|exists:outlets,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['nama', 'harga', 'kategori']);

        if ($request->hasFile('gambar')) {
            if ($produk->image && Storage::disk('public')->exists($produk->image)) {
                Storage::disk('public')->delete($produk->image);
            }
            $data['image'] = $request->file('gambar')->store('produk-images', 'public');
        }

        $produk->update($data);

        if ($request->filled('outlet_ids')) {
            $outletIds = Outlet::query()
                ->where('tenant_id', $tenant->id)
                ->whereIn('id', $request->input('outlet_ids', []))
                ->pluck('id');

            ProductOutlet::query()->where('produk_id', $produk->id)->delete();

            foreach ($outletIds as $outletId) {
                ProductOutlet::create([
                    'tenant_id' => $tenant->id,
                    'produk_id' => $produk->id,
                    'outlet_id' => $outletId,
                ]);

                ProductStock::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'produk_id' => $produk->id,
                        'outlet_id' => $outletId,
                    ],
                    [
                        'stock' => 0,
                        'minimum_stock' => 0,
                    ]
                );
            }
        }

        if ($request->has('stok')) {
            ProductStock::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'produk_id' => $produk->id,
                    'outlet_id' => $currentOutlet->id,
                ],
                [
                    'stock' => (int) $request->stok,
                ]
            );
        }

        return response()->json($produk);
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->currentTenant($request);
        $produk = Produk::query()->where('tenant_id', $tenant->id)->find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        if ($produk->image && Storage::disk('public')->exists($produk->image)) {
            Storage::disk('public')->delete($produk->image);
        }

        $produk->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    public function updateStok(Request $request, $id)
    {
        $tenant = $this->currentTenant($request);
        $outlet = $this->currentOutlet($request);
        $produk = Produk::query()->where('tenant_id', $tenant->id)->find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $request->validate(['jumlah' => 'required|integer']);
        $stock = ProductStock::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'produk_id' => $produk->id,
                'outlet_id' => $outlet->id,
            ],
            [
                'stock' => 0,
                'minimum_stock' => 0,
            ]
        );

        $newStok = $stock->stock + $request->jumlah;

        if ($newStok < 0) {
            return response()->json(['message' => 'Stok tidak mencukupi'], 400);
        }

        $stock->update(['stock' => $newStok]);
        $produk->stok = $newStok;

        return response()->json($produk);
    }
}
