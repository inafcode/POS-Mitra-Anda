<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class InstansiController extends Controller
{
    // 1. Halaman Daftar Instansi (Di Ruang Admin)
    public function index()
    {
        $instansis = User::where('role', 'instansi')
                        ->with(['transactions' => function($q) {
                            // Hitung 'Belum Lunas' DAN 'Menunggu Pembayaran' sebagai tagihan aktif
                            $q->whereIn('status_pembayaran', ['Belum Lunas', 'Menunggu Pembayaran']);
                        }])
                        ->latest()
                        ->get();

        return view('admin.instansi', compact('instansis'));
    }

    // 2. Tambah Akun Mitra Instansi Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'instansi',
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mitra Instansi baru berhasil didaftarkan!');
    }

    // 3. Reset Password / Edit Data Instansi
    public function update(Request $request, $id)
    {
        $instansi = User::where('role', 'instansi')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $instansi->id,
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $instansi->update($data);

        return back()->with('success', 'Data Mitra Instansi berhasil diperbarui!');
    }

    // 4. Hapus Mitra Instansi (Dengan Proteksi Tunggakan)
    public function destroy($id)
    {
        $instansi = User::where('role', 'instansi')->findOrFail($id);
        
        // PROTEKSI GUARD: Cek apakah masih ada tunggakan
        $adaTunggakan = Transaction::where('instansi_id', $id)
                            ->where('status_pembayaran', 'Belum Lunas')
                            ->exists();
                            
        if($adaTunggakan) {
            return back()->with('error', 'Gagal Dihapus! Mitra ini masih memiliki nota bon yang belum lunas. Lunasi terlebih dahulu.');
        }

        $instansi->delete();
        return back()->with('success', 'Mitra Instansi berhasil dihapus dari sistem!');
    }

    // 5. Halaman Laporan / Rekap Bon Instansi per Bulan
    public function laporanBon(Request $request, $id)
    {
        $instansi = User::where('role', 'instansi')->findOrFail($id);
        $bulanPilih = $request->query('bulan', date('Y-m'));

        $transactions = Transaction::with('details')
                        ->where('instansi_id', $instansi->id)
                        ->whereIn('metode_pembayaran', ['Bon / Tempo', 'Transfer'])
                        ->whereYear('created_at', substr($bulanPilih, 0, 4))
                        ->whereMonth('created_at', substr($bulanPilih, 5, 2))
                        ->latest()
                        ->get();

        // Hitung akumulasi hutang termasuk yang masih "Menunggu Pembayaran"
        $totalHutangAktif = Transaction::where('instansi_id', $instansi->id)
                            ->whereIn('metode_pembayaran', ['Bon / Tempo', 'Transfer'])
                            ->whereIn('status_pembayaran', ['Belum Lunas', 'Menunggu Pembayaran']) // <-- PERBAIKAN DI SINI
                            ->sum('total_bayar');

        $totalBelanjaBulanIni = $transactions->sum('total_bayar');
        $totalSudahLunasBulanIni = $transactions->where('status_pembayaran', 'Lunas')->sum('total_bayar');

        return view('admin.laporan_bon_instansi', compact(
            'instansi', 'transactions', 'bulanPilih', 
            'totalHutangAktif', 'totalBelanjaBulanIni', 'totalSudahLunasBulanIni'
        ));
    }

    // 6. Lunasi Bon Satuan
    public function lunasiBon($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['status_pembayaran' => 'Lunas']);

        return back()->with('success', 'Nota bon berhasil dilunasi!');
    }

    // 7. Export Excel Rekap Bon
    public function exportExcelBon(Request $request, $id)
    {
        $instansi = User::where('role', 'instansi')->findOrFail($id);
        $bulanPilih = $request->query('bulan', date('Y-m'));

        $transactions = Transaction::with('details')
                        ->where('instansi_id', $instansi->id)
                        ->where('metode_pembayaran', 'Bon / Tempo')
                        ->whereYear('created_at', substr($bulanPilih, 0, 4))
                        ->whereMonth('created_at', substr($bulanPilih, 5, 2))
                        ->latest()
                        ->get();

        $namaFile = 'Rekap_Bon_' . preg_replace('/[^A-Za-z0-9]/', '_', $instansi->name) . '_' . $bulanPilih . '.xls';

        return response()->view('admin.laporan_bon_excel', compact('instansi', 'transactions', 'bulanPilih'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $namaFile . '"');
    }

    // 8. Pelunasan Massal (Mencakup Approve Transfer)
    public function prosesPelunasanMassal(Request $request, $id)
    {
        $notaDipilih = $request->input('nota_ids', []);
        $bulanPilih = $request->input('bulan', date('Y-m'));

        $transaksiBulanIni = Transaction::where('instansi_id', $id)
            ->whereIn('metode_pembayaran', ['Bon / Tempo', 'Transfer'])
            ->whereYear('created_at', substr($bulanPilih, 0, 4))
            ->whereMonth('created_at', substr($bulanPilih, 5, 2))
            ->get();

        // Loop untuk update status secara dinamis
        foreach ($transaksiBulanIni as $trx) {
            if (in_array($trx->id, $notaDipilih)) {
                // Jika dicentang admin = Lunas
                $trx->update(['status_pembayaran' => 'Lunas']);
            } else {
                // Jika tidak dicentang, kembalikan ke status asalnya
                $statusAsal = ($trx->metode_pembayaran === 'Transfer') ? 'Menunggu Pembayaran' : 'Belum Lunas';
                $trx->update(['status_pembayaran' => $statusAsal]);
            }
        }

        return redirect("/admin/instansi/{$id}/laporan?bulan={$bulanPilih}")
            ->with('success', 'Status tagihan instansi berhasil diperbarui!');
    }

    // 9. Cetak Nota Instansi dari Admin
    public function cetakNotaInstansi($id)
    {
        $transaction = Transaction::with(['details', 'instansi'])->findOrFail($id);
        return view('admin.cetak_nota_instansi', compact('transaction'));
    }

    // 10. Cetak Kwitansi Tanda Terima Pelunasan
    public function cetakKwitansi(Request $request, $id)
    {
        $bulanPilih = $request->query('bulan', date('Y-m'));
        $instansi = User::findOrFail($id);
        
        $transactions = Transaction::where('instansi_id', $id)
            ->where('metode_pembayaran', 'Bon / Tempo')
            ->where('status_pembayaran', 'Lunas')
            ->whereYear('created_at', substr($bulanPilih, 0, 4))
            ->whereMonth('created_at', substr($bulanPilih, 5, 2))
            ->orderBy('created_at', 'asc')
            ->get();
            
        $totalPelunasan = $transactions->sum('total_bayar');

        return view('admin.kwitansi_pelunasan', compact('instansi', 'transactions', 'bulanPilih', 'totalPelunasan'));
    }


    // --- FITUR PORTAL INSTANSI (B2B) ---

    // 1. Halaman Dashboard Portal Instansi
    public function portal(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'instansi') return redirect('/admin');

        $searchProduk = $request->query('q');
        
        $productQuery = Product::where('stok', '>', 0);
        if ($searchProduk) {
            $productQuery->where(function($q) use ($searchProduk) {
                $q->where('nama_item', 'like', '%' . $searchProduk . '%')
                  ->orWhere('barcode', 'like', '%' . $searchProduk . '%');
            });
        }
        $products = $productQuery->get();

        $cartItems = Cart::where('instansi_id', $user->id)->with('product')->get();
        
        // Tampilkan tagihan Bon dan Transfer yang belum dikonfirmasi
        $tagihanAktif = Transaction::with('details')
            ->where('instansi_id', $user->id)
            ->whereIn('metode_pembayaran', ['Bon / Tempo', 'Transfer'])
            ->whereIn('status_pembayaran', ['Belum Lunas', 'Menunggu Pembayaran']) // <-- PERBAIKAN DI SINI
            ->latest()
            ->get();
            
        $totalHutangAktif = $tagihanAktif->sum('total_bayar');

        return view('instansi.portal', compact('user', 'products', 'searchProduk', 'cartItems', 'tagihanAktif', 'totalHutangAktif'));
    }

    // 2. Tambah ke Keranjang
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        if ($product->stok < $request->qty) {
            return back()->with('error', 'Gagal! Stok barang di gudang tidak mencukupi.');
        }

        $cart = Cart::where('instansi_id', $user->id)->where('product_id', $request->product_id)->first();

        if ($cart) {
            $newQty = $cart->qty + $request->qty;
            if ($product->stok < $newQty) return back()->with('error', 'Gagal! Total pesanan melebihi stok gudang.');
            $cart->update(['qty' => $newQty]);
        } else {
            Cart::create([
                'instansi_id' => $user->id,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
            ]);
        }

        return back()->with('success', 'Barang berhasil dimasukkan ke keranjang belanja!');
    }

    // 3. Hapus dari Keranjang
    public function removeFromCart($id)
    {
        Cart::where('instansi_id', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'Barang dihapus dari keranjang.');
    }

    // 4. Checkout (Pilihan Bon atau Transfer to WA)
    public function checkoutCart(Request $request)
    {
        $user = Auth::user();
        $cartItems = Cart::where('instansi_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang belanja Anda kosong.');
        }

        $totalBayar = 0;
        foreach ($cartItems as $item) {
            if ($item->qty > $item->product->stok) {
                return back()->with('error', 'Stok produk ' . $item->product->nama_item . ' habis/kurang. Silakan kurangi qty.');
            }
            $totalBayar += ($item->product->harga_jual_ecer * $item->qty);
        }

        // Tangkap pilihan metode
        $metode = $request->input('metode_pembayaran', 'Bon / Tempo');
        
        $status = ($metode === 'Transfer') ? 'Menunggu Pembayaran' : 'Belum Lunas';
        $kodeNota = 'WEB-' . rand(100000, 999999);

        // Buat Nota Baru
        $transaction = Transaction::create([
            'instansi_id' => $user->id,
            'kode_nota' => $kodeNota,
            'metode_pembayaran' => $metode,
            'status_pembayaran' => $status,
            'total_bayar' => $totalBayar,
            'uang_tunai' => 0,
            'kembalian' => 0,
        ]);

        foreach ($cartItems as $item) {
            $subtotal = $item->product->harga_jual_ecer * $item->qty;

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item->product->id,
                'nama_item' => $item->product->nama_item,
                'pemilik' => $item->product->pemilik,
                'qty' => $item->qty,
                'satuan' => $item->product->satuan,
                'harga_jual' => $item->product->harga_jual_ecer,
                'subtotal' => $subtotal,
            ]);

            $item->product->decrement('stok', $item->qty);
        }

        Cart::where('instansi_id', $user->id)->delete();

        // LOGIKA REDIRECT KE WHATSAPP JIKA TRANSFER
        if ($metode === 'Transfer') {
            $nomorWaAdmin = "6282255332087"; 
            
            $pesanWA = "Halo Admin Toko Mitra Anda, \n\n";
            $pesanWA .= "Saya dari instansi *{$user->name}* ingin mengonfirmasi pembayaran pesanan dengan detail:\n";
            $pesanWA .= "- Kode Nota: *{$kodeNota}*\n";
            $pesanWA .= "- Total Bayar: *Rp" . number_format($totalBayar, 0, ',', '.') . "*\n\n";
            $pesanWA .= "Berikut saya lampirkan bukti transfernya.";

            $linkWA = "https://api.whatsapp.com/send?phone={$nomorWaAdmin}&text=" . urlencode($pesanWA);
            
            // Langsung redirect (Karena form HTML nanti kita atur _blank, otomatis terbuka di tab baru)
            return redirect($linkWA);
        }

        // Jika metode BON, akan mengeksekusi baris ini (tetap di halaman yang sama)
        return back()->with('success', 'Checkout Berhasil! Pesanan Bon Anda telah masuk ke sistem toko.');
    }
}