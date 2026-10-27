<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Memanggil Kepala Gudang

class StoreController extends Controller
{
    public function index()
    {
        // Menyuruh Kepala Gudang mengambil SEMUA data barang
        $products = Product::all();
        
        // Melemparkan data tersebut ke layar depan (file 'welcome.blade.php')
        return view('welcome', compact('products'));
    }
}