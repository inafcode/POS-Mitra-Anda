<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class PosController extends Controller
{
    public function index()
    {
        return view('kasir');
    }

    // Fungsi API baru untuk mencari barang
    public function search(Request $request)
    {
        $keyword = $request->query('q');

        if (!$keyword) {
            return response()->json([]);
        }

        // 1. Cek dulu apakah kasir menembak Barcode yang persis sama
        $exactBarcode = Product::where('barcode', $keyword)->first();
        if ($exactBarcode) {
            return response()->json([$exactBarcode]);
        }

        // 2. Jika bukan barcode persis, cari semua nama barang / barcode yang mirip (maksimal 10 hasil)
        $products = Product::where('nama_item', 'like', '%' . $keyword . '%')
                    ->orWhere('barcode', 'like', '%' . $keyword . '%')
                    ->limit(10)
                    ->get();

        return response()->json($products);
    }

    // Fungsi untuk mencetak struk & memotong stok
    public function cetak(Request $request)
    {
        $cartData = json_decode($request->cart_data, true);
        $totalBayar = $request->total_bayar;
        $uangTunai = $request->uang_tunai;
        $kembalian = $request->kembalian;
        
        // MESIN POTONG STOK OTOMATIS
        foreach ($cartData as $item) {
            $product = Product::find($item['id']);
            if ($product && $product->tipe == 'Barang') {
                $product->stok = $product->stok - $item['qty'];
                $product->save();
            }
        }
        
        return view('struk', compact('cartData', 'totalBayar', 'uangTunai', 'kembalian'));
    }
}