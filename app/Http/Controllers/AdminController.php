<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <--- WAJIB ADA AGAR EDIT/HAPUS FOTO TIDAK ERROR
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;

class AdminController extends Controller
{
    // 1. Halaman Ringkasan (Buku Kas & Metrik Dashboard)
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

    public function index(Request $request)
    {
        $search = $request->query('search');
        $pemilik = $request->query('pemilik');

        $query = Product::query();

        // 1. Filter berdasarkan pencarian nama atau barcode
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_item', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        // 2. Filter berdasarkan pemilik toko (Mitra Anda / Kado Koeni)
        if ($pemilik) {
            $query->where('pemilik', $pemilik);
        }

        // Ambil data (paginate 20 item per halaman agar tidak terlalu panjang)
        $products = $query->latest()->paginate(20)->withQueryString();

        return view('admin.index', compact('products', 'search', 'pemilik'));
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

        return redirect('/admin')->with('success', 'Barang baru beserta foto berhasil ditambahkan!');
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

        return redirect('/admin')->with('success', 'Data barang dan foto berhasil diperbarui!');
    }

    // Menghapus data beserta file fotonya dari Database & Folder
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->foto_produk) {
            Storage::disk('public')->delete($product->foto_produk);
        }
        
        $product->delete();

        return redirect('/admin')->with('success', 'Barang berhasil dihapus dari gudang!');
    }

    // Halaman Katalog Publik (Pintu Depan)
    public function catalog()
    {
        $products = Product::latest()->get();
        return view('welcome', compact('products'));
    }
}