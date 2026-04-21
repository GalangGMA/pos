<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\BankAccount;
use App\Models\MKategori;
use App\Models\Outlet;
use App\Models\ProductOutlet;
use App\Models\ProductStock;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TypeBayar;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class KasirController extends Controller
{
    public function home(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'kasir') {
            return redirect('/admin');
        }

        $outlet = $this->currentOutlet($user->tenant_id, $user->outlet_id);

        if (! $outlet) {
            abort(403, 'Outlet kasir tidak valid atau tidak aktif.');
        }

        $products = $this->productsForOutlet($user->tenant_id, $outlet->id);
        $todaySummary = $this->todaySummary($user->tenant_id, $outlet->id);
        $recentTransactions = $this->recentTransactions($user->tenant_id, $outlet->id, 4);
        $availableProducts = $products->where('stok', '>', 0)->count();
        $lowStockProducts = $products->filter(fn ($product) => $product['stok'] > 0 && $product['stok'] <= 5)->count();
        $emptyStockProducts = $products->where('stok', '<=', 0)->count();

        return view('kasir.home', [
            'outlet' => $outlet,
            'todaySummary' => $todaySummary,
            'recentTransactions' => $recentTransactions,
            'availableProducts' => $availableProducts,
            'lowStockProducts' => $lowStockProducts,
            'emptyStockProducts' => $emptyStockProducts,
        ]);
    }

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'kasir') {
            return redirect('/admin');
        }

        $outlet = $this->currentOutlet($user->tenant_id, $user->outlet_id);

        if (! $outlet) {
            abort(403, 'Outlet kasir tidak valid atau tidak aktif.');
        }

        $products = $this->productsForOutlet($user->tenant_id, $outlet->id);
        $categories = $this->categoriesForTenant($user->tenant_id);

        $paymentMethods = TypeBayar::query()
            ->orderBy('id')
            ->get(['id', 'type']);
        $bankAccounts = $this->bankAccountsForTenant($user->tenant_id);

        $todaySummary = $this->todaySummary($user->tenant_id, $outlet->id);
        $recentTransactions = $this->recentTransactions($user->tenant_id, $outlet->id, 8);

        return view('kasir.index', [
            'products' => $products,
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
            'bankAccounts' => $bankAccounts,
            'outlet' => $outlet,
            'todaySummary' => $todaySummary,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    public function products(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'kasir') {
            return redirect('/admin');
        }

        $outlet = $this->currentOutlet($user->tenant_id, $user->outlet_id);

        if (! $outlet) {
            abort(403, 'Outlet kasir tidak valid atau tidak aktif.');
        }

        return view('kasir.products', [
            'products' => $this->productsForOutlet($user->tenant_id, $outlet->id),
            'categories' => $this->categoriesForTenant($user->tenant_id),
            'outlet' => $outlet,
        ]);
    }

    public function reports(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'kasir') {
            return redirect('/admin');
        }

        $outlet = $this->currentOutlet($user->tenant_id, $user->outlet_id);

        if (! $outlet) {
            abort(403, 'Outlet kasir tidak valid atau tidak aktif.');
        }

        $todaySummary = $this->todaySummary($user->tenant_id, $outlet->id);
        $recentTransactions = $this->recentTransactions($user->tenant_id, $outlet->id, 10);
        $cashTransactions = $recentTransactions->where('metode_pembayaran', 'Tunai')->count();
        $nonCashTransactions = $recentTransactions->count() - $cashTransactions;

        return view('kasir.reports', [
            'outlet' => $outlet,
            'todaySummary' => $todaySummary,
            'recentTransactions' => $recentTransactions,
            'cashTransactions' => $cashTransactions,
            'nonCashTransactions' => $nonCashTransactions,
        ]);
    }

    public function more(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'kasir') {
            return redirect('/admin');
        }

        $outlet = $this->currentOutlet($user->tenant_id, $user->outlet_id);

        if (! $outlet) {
            abort(403, 'Outlet kasir tidak valid atau tidak aktif.');
        }

        $products = $this->productsForOutlet($user->tenant_id, $outlet->id);

        return view('kasir.more', [
            'outlet' => $outlet,
            'user' => $user,
            'productCount' => $products->count(),
            'categoryCount' => $this->categoriesForTenant($user->tenant_id)->count(),
        ]);
    }

    public function printerBluetooth(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'kasir') {
            return redirect('/admin');
        }

        $outlet = $this->currentOutlet($user->tenant_id, $user->outlet_id);

        if (! $outlet) {
            abort(403, 'Outlet kasir tidak valid atau tidak aktif.');
        }

        return view('kasir.printer-bluetooth', [
            'outlet' => $outlet,
            'user' => $user,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'kasir') {
            abort(403);
        }

        $outlet = $this->currentOutlet($user->tenant_id, $user->outlet_id);

        if (! $outlet) {
            abort(403, 'Outlet kasir tidak valid atau tidak aktif.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => ['required', 'string', 'max:100'],
            'bayar' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.produk_id' => ['required', 'integer', 'exists:produks,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            $receiptItems = [];

            foreach ($validated['items'] as $item) {
                $product = Produk::query()
                    ->where('tenant_id', $user->tenant_id)
                    ->lockForUpdate()
                    ->find($item['produk_id']);

                if (! $product) {
                    throw new \RuntimeException("Produk dengan ID {$item['produk_id']} tidak ditemukan.");
                }

                $isAvailableForOutlet = ProductOutlet::query()
                    ->where('tenant_id', $user->tenant_id)
                    ->where('produk_id', $product->id)
                    ->where('outlet_id', $outlet->id)
                    ->exists();

                if (! $isAvailableForOutlet) {
                    throw new \RuntimeException("Produk {$product->nama} tidak tersedia di outlet ini.");
                }

                $stock = ProductStock::query()
                    ->where('tenant_id', $user->tenant_id)
                    ->where('produk_id', $product->id)
                    ->where('outlet_id', $outlet->id)
                    ->lockForUpdate()
                    ->first();

                if (! $stock || $stock->stock < $item['qty']) {
                    throw new \RuntimeException("Stok produk {$product->nama} tidak mencukupi.");
                }

                $total += $product->harga * $item['qty'];
                $receiptItems[] = [
                    'nama' => $product->nama,
                    'qty' => (int) $item['qty'],
                    'harga' => (float) $product->harga,
                    'subtotal' => (float) ($product->harga * $item['qty']),
                ];
            }

            $bayar = (float) $validated['bayar'];
            $isCashPayment = $validated['metode_pembayaran'] === 'Tunai';

            if ($isCashPayment && $bayar < $total) {
                throw new \RuntimeException('Nominal tunai lebih kecil dari total transaksi.');
            }

            if (! $isCashPayment) {
                $bayar = $total;
            }

            $now = Carbon::now();
            $today = $now->toDateString();
            $lastTransaction = Transaksi::query()
                ->where('tenant_id', $user->tenant_id)
                ->where('outlet_id', $outlet->id)
                ->whereDate('tanggal', $today)
                ->orderByDesc('no_transaksi')
                ->lockForUpdate()
                ->first();

            $sequence = 1;

            if ($lastTransaction) {
                $parts = explode('/', $lastTransaction->no_transaksi ?? '');
                $sequence = (int) end($parts) + 1;
            }

            $transactionNumber = sprintf('TRX/%s/%s/%03d', $outlet->code, $now->format('Ymd'), $sequence);
            $change = $isCashPayment ? max(0, $bayar - $total) : 0;

            $transaction = Transaksi::query()->create([
                'tenant_id' => $user->tenant_id,
                'outlet_id' => $outlet->id,
                'no_transaksi' => $transactionNumber,
                'tanggal' => $now,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $change,
                'metode_pembayaran' => $validated['metode_pembayaran'],
            ]);

            foreach ($validated['items'] as $item) {
                $product = Produk::query()
                    ->where('tenant_id', $user->tenant_id)
                    ->findOrFail($item['produk_id']);

                $subtotal = $product->harga * $item['qty'];

                DetailTransaksi::query()->create([
                    'tenant_id' => $user->tenant_id,
                    'transaksi_id' => $transaction->id,
                    'produk_id' => $product->id,
                    'jumlah' => $item['qty'],
                    'harga' => $product->harga,
                    'subtotal' => $subtotal,
                ]);

                ProductStock::query()
                    ->where('tenant_id', $user->tenant_id)
                    ->where('produk_id', $product->id)
                    ->where('outlet_id', $outlet->id)
                    ->decrement('stock', $item['qty']);
            }

            DB::commit();

            return redirect()
                ->route('kasir.index')
                ->with('success', "Transaksi {$transactionNumber} berhasil disimpan.")
                ->with('receipt', [
                    'outlet_name' => $outlet->name,
                    'outlet_address' => $outlet->address,
                    'outlet_phone' => $outlet->phone,
                    'cashier_name' => $user->name,
                    'transaction_number' => $transactionNumber,
                    'transaction_date' => $now->toIso8601String(),
                    'payment_method' => $validated['metode_pembayaran'],
                    'items' => $receiptItems,
                    'total' => (float) $total,
                    'bayar' => (float) $bayar,
                    'kembalian' => (float) $change,
                ]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            return redirect()
                ->route('kasir.index')
                ->withErrors(['checkout' => $exception->getMessage()]);
        }
    }

    private function currentOutlet(?int $tenantId, ?int $outletId): ?Outlet
    {
        if (! $tenantId || ! $outletId) {
            return null;
        }

        return Outlet::query()
            ->where('tenant_id', $tenantId)
            ->whereKey($outletId)
            ->where('is_active', true)
            ->first();
    }

    private function productsForOutlet(int $tenantId, int $outletId)
    {
        return Produk::query()
            ->where('tenant_id', $tenantId)
            ->whereHas('outletMappings', fn ($query) => $query->where('outlet_id', $outletId))
            ->with([
                'stocks' => fn ($query) => $query->where('outlet_id', $outletId),
                'kategori:id,nama',
            ])
            ->orderBy('nama')
            ->get()
            ->map(function (Produk $product) use ($outletId) {
                $stock = $product->stocks->firstWhere('outlet_id', $outletId);

                return [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'harga' => (float) $product->harga,
                    'stok' => (int) ($stock->stock ?? 0),
                    'kategori' => $product->kategori->nama ?? $product->getAttribute('kategori') ?? 'Lainnya',
                    'kategori_id' => $product->kategori_id,
                    'image_url' => $product->image ? URL::to('storage/' . $product->image) : null,
                ];
            })
            ->values();
    }

    private function categoriesForTenant(int $tenantId)
    {
        return MKategori::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('nama')
            ->get(['id', 'nama'])
            ->map(fn (MKategori $category) => [
                'id' => $category->id,
                'nama' => $category->nama,
            ])
            ->values();
    }

    private function bankAccountsForTenant(int $tenantId)
    {
        return BankAccount::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('bank_name')
            ->get(['id', 'bank_name', 'account_number', 'account_holder', 'note'])
            ->map(fn (BankAccount $account) => [
                'id' => $account->id,
                'bank_name' => $account->bank_name,
                'account_number' => $account->account_number,
                'account_holder' => $account->account_holder,
                'note' => $account->note,
            ])
            ->values();
    }

    private function todaySummary(int $tenantId, int $outletId): array
    {
        $today = now();
        $summary = Transaksi::query()
            ->where('tenant_id', $tenantId)
            ->where('outlet_id', $outletId)
            ->whereDate('tanggal', $today->toDateString())
            ->selectRaw('COUNT(*) as total_transaksi, COALESCE(SUM(total), 0) as omzet_harian')
            ->first();

        return [
            'total_transaksi' => (int) ($summary->total_transaksi ?? 0),
            'omzet_harian' => (float) ($summary->omzet_harian ?? 0),
        ];
    }

    private function recentTransactions(int $tenantId, int $outletId, int $limit)
    {
        return Transaksi::query()
            ->where('tenant_id', $tenantId)
            ->where('outlet_id', $outletId)
            ->withCount('details')
            ->latest('id')
            ->limit($limit)
            ->get(['id', 'no_transaksi', 'total', 'metode_pembayaran', 'tanggal'])
            ->map(fn (Transaksi $transaction) => [
                'no_transaksi' => $transaction->no_transaksi,
                'total' => (float) $transaction->total,
                'metode_pembayaran' => $transaction->metode_pembayaran,
                'jumlah_item' => (int) $transaction->details_count,
                'tanggal' => optional($transaction->tanggal)->format('d M Y H:i'),
            ]);
    }
}
