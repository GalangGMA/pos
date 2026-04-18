<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('created_at', 'DESC')->get();
        // Tambahkan URL penuh untuk setiap image
        $produk->transform(function($item) {
            $item->image_url = $item->image ? URL::to('storage/'.$item->image) : null;
            return $item;
        });
        return response()->json($produk);
    }

    public function show($id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }
        return response()->json($produk);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'gambar' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['nama', 'harga', 'stok']);
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk-images', 'public');
        }

        $produk = Produk::create($data);
        return response()->json($produk, 201);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|string|max:255',
            'harga' => 'sometimes|numeric',
            'stok' => 'sometimes|integer',
            'gambar' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['nama', 'harga', 'stok']);
        if ($request->hasFile('gambar')) {
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('produk-images', 'public');
        }

        $produk->update($data);
        return response()->json($produk);
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    public function updateStok(Request $request, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $request->validate(['jumlah' => 'required|integer']);
        $newStok = $produk->stok + $request->jumlah;

        if ($newStok < 0) {
            return response()->json(['message' => 'Stok tidak mencukupi'], 400);
        }

        $produk->update(['stok' => $newStok]);
        return response()->json($produk);
    }
}
