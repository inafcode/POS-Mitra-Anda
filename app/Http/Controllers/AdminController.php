<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <--- WAJIB ADA AGAR EDIT/HAPUS FOTO TIDAK ERROR
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    // Method untuk halaman Ringkasan (URL: /admin)
    public function dashboard()
    {
        $hariIni = Carbon::today();

        $pendapatanHariIni = Transaction::whereDate('created_at', $hariIni)
            ->where('status_pembayaran', 'Lunas')
            ->sum('total_bayar');

        $pesananPerluDiproses = Transaction::with('instansi')
            ->whereIn('status_pembayaran', ['Menunggu Pembayaran', 'Belum Lunas'])
            ->latest()
            ->take(5)
            ->get();
        $totalPerluDiproses = Transaction::whereIn('status_pembayaran', ['Menunggu Pembayaran', 'Belum Lunas'])->count();

        $stokMenipis = Product::where('tipe', 'Barang')
            ->where('stok', '<', 5)
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get();
        $totalStokMenipis = Product::where('tipe', 'Barang')->where('stok', '<', 5)->count();

        $totalPiutang = Transaction::where('status_pembayaran', 'Belum Lunas')->sum('total_bayar');
        $produkTerlaris = Product::orderBy('stok', 'asc')->take(5)->get();

        // Mengarah ke file dashboard.blade.php
        return view('admin.dashboard', compact(
            'pendapatanHariIni',
            'pesananPerluDiproses',
            'totalPerluDiproses',
            'stokMenipis',
            'totalStokMenipis',
            'totalPiutang',
            'produkTerlaris'
        ));
    }

    // Method untuk CRUD Katalog Barang (URL: /admin/produk)
    public function index(Request $request)
    {
        $pemilik = $request->query('pemilik');
        $search = $request->query('search');

        $query = Product::query();

        if ($pemilik) {
            $query->where('pemilik', $pemilik);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_item', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->get();

        // PASTIKAN BARIS INI: me-return view 'admin.produk', BUKAN 'admin.index' atau 'admin.dashboard'
        return view('admin.produk', compact('products', 'pemilik', 'search'));
    }

    // Fungsi untuk Halaman Katalog Barang (/admin/produk)
    public function produk(Request $request)
    {
        $pemilik = $request->query('pemilik');
        $search = $request->query('search');

        $query = Product::query();

        if ($pemilik) {
            $query->where('pemilik', $pemilik);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_item', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.produk', compact('products', 'pemilik', 'search'));
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
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();
        $data['stok'] = $data['stok'] ?? 0; // Pengaman jika tipe Jasa dan stok dikosongkan
        $data['harga_modal'] = $data['harga_modal'] ?? 0;

        // MESIN PEMBUAT BARCODE CERDAS (3 Huruf Nama + Strip + 5 Angka)
        if (empty($data['barcode'])) {
            $tigaHuruf = strtoupper(substr(trim($data['nama_item']), 0, 3));
            $data['barcode'] = $tigaHuruf . '-' . rand(10000, 99999);
        }

        // Jika kasir mengupload foto, simpan ke folder 'public/produk'
        if ($request->hasFile('foto_produk')) {
            $path = $request->file('foto_produk')->store('produk', 'public');
            $data['foto_produk'] = $path;
        }

        Product::create($data);

        return redirect('/admin/produk')->with('success', 'Barang baru berhasil ditambahkan!');
    }

    // Membuka halaman Formulir Edit
    public function edit($id)
    {
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
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $product = Product::findOrFail($id);
        $data = $request->all();
        $data['stok'] = $data['stok'] ?? 0;
        $data['harga_modal'] = $data['harga_modal'] ?? 0;

        // Jika kasir mengupload foto baru saat mengedit
        if ($request->hasFile('foto_produk')) {
            if ($product->foto_produk) {
                Storage::disk('public')->delete($product->foto_produk);
            }
            $path = $request->file('foto_produk')->store('produk', 'public');
            $data['foto_produk'] = $path;
        }

        $product->update($data); 

        return redirect('/admin/produk')->with('success', 'Data barang dan foto berhasil diperbarui!');
    }

    // Menghapus data beserta file fotonya dari Database & Folder
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->foto_produk) {
            Storage::disk('public')->delete($product->foto_produk);
        }
        
        $product->delete();

        return redirect('/admin/produk')->with('success', 'Barang berhasil dihapus dari gudang!');
    }

    // Halaman Katalog Publik (Pintu Depan)
    public function catalog()
    {
        $products = Product::latest()->get();
        return view('welcome', compact('products'));
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ProductsImport, $request->file('file_excel'));

        return redirect('/admin/produk')->with('success', 'Ratusan data barang berhasil diimpor dari file Excel!');
    }
}