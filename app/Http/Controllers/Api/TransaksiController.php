<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\TypeBayar;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['details.produk'])
            ->orderBy('tanggal', 'DESC')
            ->get();

        return response()->json($transaksis);
    }

    public function getDetailAll()
    {
        $details = DetailTransaksi::with('produk')->get();
        return response()->json($details);
    }

    public function getDetail($id)
    {
        $details = DetailTransaksi::with('produk')
            ->where('transaksi_id', $id)
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
                $produk = Produk::lockForUpdate()->find($item['produk_id']);
                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$item['produk_id']} tidak ditemukan");
                }

                if ($produk->stok < $item['qty']) {
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
                ->orderByDesc('no_transaksi')
                ->lockForUpdate()
                ->first();

            $noUrut = 1;
            if ($lastTransaksi) {
                $lastNo = explode('/', $lastTransaksi->no_transaksi);
                $noUrut = (int) end($lastNo) + 1;
            }

            $no_transaksi = sprintf("TRX/%s/%03d", str_replace('-', '', $tanggal), $noUrut);

            // Simpan transaksi
            $transaksi = Transaksi::create([
                'no_transaksi' => $no_transaksi,
                'tanggal' => $tanggal,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
            ]);

            // Detail transaksi & update stok
            foreach ($items as $item) {
                $produk = Produk::find($item['produk_id']);
                $subtotal = $produk->harga * $item['qty'];

                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $produk->id,
                    'jumlah' => $item['qty'],
                    'harga' => $produk->harga,
                    'subtotal' => $subtotal
                ]);

                $produk->decrement('stok', $item['qty']);
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
