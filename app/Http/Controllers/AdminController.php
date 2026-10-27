<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Memanggil Produk (Model) untuk CRUD

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data barang, diurutkan dari yang terbaru
        $products = Product::latest()->get(); 
        
        return view('admin.index', compact('products'));
    }

    // Membuka halaman Formulir Tambah Barang
    public function create()
    {
        return view('admin.create');
    }

    // Menangkap isian formulir dan menyimpannya ke Database
    public function store(Request $request)
    {
        $request->validate([
            'nama_item' => 'required',
            'tipe' => 'required',
            'harga_jual_ecer' => 'required|numeric',
            'foto_produk' => 'image|mimes:jpeg,png,jpg|max:2048' // Maksimal 2MB
        ]);

        $data = $request->all();

        // MESIN PEMBUAT BARCODE CERDAS (3 Huruf Nama + Strip + 5 Angka)
        if (empty($data['barcode'])) {
            // Ambil 3 huruf pertama dari nama barang, hapus spasi berlebih, dan jadikan Huruf Kapital
            $tigaHuruf = strtoupper(substr(trim($data['nama_item']), 0, 3));
            
            // Gabungkan 3 huruf tersebut dengan strip dan 5 angka acak (contoh: PUL-83921)
            $data['barcode'] = $tigaHuruf . '-' . rand(10000, 99999);
        }

        // Jika kasir mengupload foto, simpan ke folder 'public/produk'
        if ($request->hasFile('foto_produk')) {
            $path = $request->file('foto_produk')->store('produk', 'public');
            $data['foto_produk'] = $path;
        }

        Product::create($data);

        return redirect('/admin')->with('success', 'Barang baru beserta foto berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    // Membuka halaman Formulir Edit
    public function edit($id)
    {
        // Cari barang berdasarkan ID
        $product = Product::findOrFail($id); 
        return view('admin.edit', compact('product'));
    }

    // Menyimpan perubahan data ke Database
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_item' => 'required',
            'tipe' => 'required',
            'harga_jual_ecer' => 'required|numeric',
            'foto_produk' => 'image|mimes:jpeg,png,jpg|max:2048' // Validasi keamanan foto
        ]);

        $product = Product::findOrFail($id);
        $data = $request->all();

        // Jika kasir mengupload foto baru saat mengedit
        if ($request->hasFile('foto_produk')) {
            // 1. Hapus foto lama dari dalam folder (jika sebelumnya sudah ada foto)
            if ($product->foto_produk) {
                Storage::disk('public')->delete($product->foto_produk);
            }
            
            // 2. Simpan foto yang baru
            $path = $request->file('foto_produk')->store('produk', 'public');
            $data['foto_produk'] = $path;
        }

        $product->update($data); 

        return redirect('/admin')->with('success', 'Data barang dan foto berhasil diperbarui!');
    }

    // Menghapus data dari Database
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Hapus permanen

        return redirect('/admin')->with('success', 'Barang berhasil dihapus dari gudang!');
    }
}
