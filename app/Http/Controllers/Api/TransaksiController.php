<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ResolvesTenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\ProductOutlet;
use App\Models\ProductStock;
use App\Models\Produk;
use App\Models\TypeBayar;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    use ResolvesTenantContext;

    public function index(Request $request)
    {
        $tenant = $this->currentTenant($request);
        $outlet = $this->currentOutlet($request);

        $transaksis = Transaksi::with(['details.produk'])
            ->where('tenant_id', $tenant->id)
            ->where('outlet_id', $outlet->id)
            ->orderBy('tanggal', 'DESC')
            ->get();

        return response()->json($transaksis);
    }

    public function getDetailAll(Request $request)
    {
        $tenant = $this->currentTenant($request);
        $user = $this->currentUser($request);

        $accessibleOutletIds = $this->accessibleOutlets($user)->pluck('id');

        if ($user->role === 'kasir') {
            $reportOutletIds = $accessibleOutletIds;
        } elseif ($request->query('scope') === 'all') {
            $reportOutletIds = $accessibleOutletIds;
        } elseif ($request->filled('outlet_id')) {
            $requestedOutletId = (int) $request->query('outlet_id');

            if (! $accessibleOutletIds->contains($requestedOutletId)) {
                return response()->json([
                    'message' => 'Outlet laporan tidak ditemukan atau tidak bisa diakses.',
                ], 422);
            }

            $reportOutletIds = collect([$requestedOutletId]);
        } else {
            $outlet = $this->currentOutlet($request);
            $reportOutletIds = collect([$outlet->id]);
        }

        $details = DetailTransaksi::with(['produk', 'transaksi.outlet'])
            ->where('tenant_id', $tenant->id)
            ->whereHas('transaksi', fn ($query) => $query->whereIn('outlet_id', $reportOutletIds))
            ->latest('id')
            ->get();

        return response()->json($details);
    }

    public function getDetail(Request $request, $id)
    {
        $tenant = $this->currentTenant($request);
        $outlet = $this->currentOutlet($request);

        $details = DetailTransaksi::with('produk')
            ->where('tenant_id', $tenant->id)
            ->where('transaksi_id', $id)
            ->whereHas('transaksi', fn ($query) => $query->where('outlet_id', $outlet->id))
            ->get();

        return response()->json($details);
    }

    public function typeBayar()
    {
        $types = TypeBayar::orderBy('id', 'ASC')->get();
        return response()->json($types);
    }

    public function store(Request $request)
    {
        $tenant = $this->currentTenant($request);
        $outlet = $this->currentOutlet($request);

        $request->validate([
            'items' => 'required|array',
            'items.*.produk_id' => 'required|integer|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'bayar' => 'required|numeric|min:0'
        ]);

        $items = $request->items;
        $bayar = $request->bayar;

        $total = 0;

        DB::beginTransaction();
        try {
            // Hitung total & validasi stok
            foreach ($items as $item) {
                $produk = Produk::query()
                    ->where('tenant_id', $tenant->id)
                    ->lockForUpdate()
                    ->find($item['produk_id']);

                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$item['produk_id']} tidak ditemukan");
                }

                $isAvailableForOutlet = ProductOutlet::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('produk_id', $produk->id)
                    ->where('outlet_id', $outlet->id)
                    ->exists();

                if (! $isAvailableForOutlet) {
                    throw new \Exception("Produk {$produk->nama} tidak tersedia di outlet ini");
                }

                $stock = ProductStock::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('produk_id', $produk->id)
                    ->where('outlet_id', $outlet->id)
                    ->lockForUpdate()
                    ->first();

                if (! $stock || $stock->stock < $item['qty']) {
                    throw new \Exception("Stok produk {$produk->nama} tidak mencukupi");
                }

                $total += $produk->harga * $item['qty'];
            }
            $kembalian = 0;
            // Validasi bayar
            if($request->metode_pembayaran == 'Tunai' && $bayar >= $total) {
                $kembalian = $bayar - $total;
            }
            $tanggal = Carbon::now()->format('Y-m-d');

            // Generate no_transaksi
            $lastTransaksi = Transaksi::whereDate('tanggal', $tanggal)
                ->where('tenant_id', $tenant->id)
                ->where('outlet_id', $outlet->id)
                ->orderByDesc('no_transaksi')
                ->lockForUpdate()
                ->first();

            $noUrut = 1;
            if ($lastTransaksi) {
                $lastNo = explode('/', $lastTransaksi->no_transaksi);
                $noUrut = (int) end($lastNo) + 1;
            }

            $no_transaksi = sprintf("TRX/%s/%s/%03d", $outlet->code, str_replace('-', '', $tanggal), $noUrut);

            // Simpan transaksi
            $transaksi = Transaksi::create([
                'tenant_id' => $tenant->id,
                'outlet_id' => $outlet->id,
                'no_transaksi' => $no_transaksi,
                'tanggal' => $tanggal,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            // Detail transaksi & update stok
            foreach ($items as $item) {
                $produk = Produk::query()->where('tenant_id', $tenant->id)->find($item['produk_id']);
                $subtotal = $produk->harga * $item['qty'];

                DetailTransaksi::create([
                    'tenant_id' => $tenant->id,
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $produk->id,
                    'jumlah' => $item['qty'],
                    'harga' => $produk->harga,
                    'subtotal' => $subtotal
                ]);

                ProductStock::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('produk_id', $produk->id)
                    ->where('outlet_id', $outlet->id)
                    ->decrement('stock', $item['qty']);
            }

            DB::commit();

            $transaksi->load('details.produk');

            return response()->json([
                'message' => 'Transaksi berhasil disimpan',
                'transaksi' => $transaksi,
                'kembalian' => $kembalian
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
