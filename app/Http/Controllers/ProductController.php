<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Product::latest()->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required',
            'harga'     => 'required',
            'stok'      => 'required',
            'deskripsi' => 'required',
            'gambar'    => 'nullable|image'
        ]);

        $gambarPath = null;
        if ($request->hasFile("gambar")) {
            $namaGambar = time() . "." . $request->gambar->extension();
            // Menyimpan di folder storage/app/public/products
            $request->gambar->storeAs("products", $namaGambar, "public");
            $gambarPath = "products/" . $namaGambar;
        }

        $product = Product::create([
            'nama'      => $request->nama,
            'harga'     => $request->harga,
            'stok'      => $request->stok,
            'deskripsi' => $request->deskripsi,
            'gambar'    => $gambarPath
        ]);

        return response()->json([
            'message' => 'Data berhasil ditambahkan',
            'data'    => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
   /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    // 1. Validasi data
    $request->validate([
        'nama'      => 'required',
        'harga'     => 'required|numeric',
        'stok'      => 'required|integer',
        'deskripsi' => 'required',
        'gambar'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Hanya validasi jika berupa file gambar
    ]);

    // 2. Update field teks
    $product->nama = $request->nama;
    $product->harga = $request->harga;
    $product->stok = $request->stok;
    $product->deskripsi = $request->deskripsi;

    // 3. Cek jika mengunggah file gambar baru
    if ($request->hasFile("gambar")) {
        // Hapus gambar lama jika ada
        if ($product->gambar && Storage::disk('public')->exists($product->gambar)) {
            Storage::disk('public')->delete($product->gambar);
        }

        $namaGambar = time() . "." . $request->gambar->extension();
        $request->gambar->storeAs("products", $namaGambar, "public");
        $product->gambar = "products/" . $namaGambar;
    }

    $product->save();

    return response()->json([
        'message' => 'Data berhasil diubah',
        'data'    => $product
    ], 200);
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Hapus file gambar fisik di folder storage saat produk dihapus
        if ($product->gambar && Storage::disk('public')->exists($product->gambar)) {
            Storage::disk('public')->delete($product->gambar);
        }

        $product->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
