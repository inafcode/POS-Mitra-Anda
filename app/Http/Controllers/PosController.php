<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Expense;
use App\Models\DailyCashflow;

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

    // Fungsi cetak struk + simpan riwayat transaksi + potong stok
    public function cetak(Request $request)
    {
        $cartData = json_decode($request->cart_data, true);
        $totalBayar = $request->total_bayar;
        $uangTunai = $request->uang_tunai;
        $kembalian = $request->kembalian;
        $metodePembayaran = $request->metode_pembayaran ?? 'Tunai';

        // Buat ID Transaksi berurutan (#4801, #4802, dst) dan anti-kembar
        $nextId = (Transaction::max('id') ?? 0) + 1;
        $kodeNota = '#' . (4800 + $nextId);

        $trx = Transaction::create([
            'kode_nota' => $kodeNota,
            'metode_pembayaran' => $metodePembayaran,
            'total_bayar' => $totalBayar,
            'uang_tunai' => $uangTunai,
            'kembalian' => $kembalian,
        ]);
        
        foreach ($cartData as $item) {
            $product = Product::find($item['id']);
            $pemilik = $product ? $product->pemilik : 'Mitra Anda';
            $satuan = $product ? $product->satuan : 'pcs';

            TransactionDetail::create([
                'transaction_id' => $trx->id,
                'product_id' => $item['id'],
                'nama_item' => $item['nama_item'],
                'pemilik' => $pemilik,
                'qty' => $item['qty'],
                'satuan' => $satuan,
                'harga_jual' => $item['harga'],
                'subtotal' => $item['subtotal'],
            ]);

            if ($product && $product->tipe == 'Barang') {
                $product->stok = $product->stok - $item['qty'];
                $product->save();
            }
        }
        
        return view('struk', compact('cartData', 'totalBayar', 'uangTunai', 'kembalian', 'kodeNota', 'metodePembayaran'));
    }

    // Halaman Laporan Cashflow Persis Format Excel (Murni Laci Tunai + Info Non-Tunai)
    public function laporan(Request $request)
    {
        $tanggal = $request->query('tanggal', date('Y-m-d'));

        // 1. Ambil Semua Detail Transaksi pada tanggal tersebut
        $details = TransactionDetail::with('transaction')
                    ->whereDate('created_at', $tanggal)
                    ->latest()
                    ->get();

        // 2. Filter HANYA transaksi TUNAI untuk dihitung ke Cashflow Laci
        $detailsTunai = $details->filter(fn($d) => ($d->transaction->metode_pembayaran ?? 'Tunai') === 'Tunai');
        $pemasukanKado = $detailsTunai->where('pemilik', 'Kado Koeni')->sum('subtotal');
        $pemasukanMitra = $detailsTunai->where('pemilik', 'Mitra Anda')->sum('subtotal');

        // 2B. Hitung Info Tambahan untuk yang masuk via QRIS / Transfer (Rekening Masing-Masing)
        $detailsNonTunai = $details->filter(fn($d) => ($d->transaction->metode_pembayaran ?? 'Tunai') !== 'Tunai');
        $nonTunaiKado = $detailsNonTunai->where('pemilik', 'Kado Koeni')->sum('subtotal');
        $nonTunaiMitra = $detailsNonTunai->where('pemilik', 'Mitra Anda')->sum('subtotal');

        // 3. Ambil Pengeluaran pada tanggal tersebut
        $expenses = Expense::whereDate('tanggal', $tanggal)->get();
        $pengeluaranKado = $expenses->where('pemilik', 'Kado Koeni')->sum('nominal');
        $pengeluaranMitra = $expenses->where('pemilik', 'Mitra Anda')->sum('nominal');

        // 4. Cek Data Cashflow Hari Ini (Jika belum ada, tarik Sisa Modal Tunai dari Hari Sebelumnya!)
        $cashflow = DailyCashflow::where('tanggal', $tanggal)->first();

        if (!$cashflow) {
            $kemarin = DailyCashflow::where('tanggal', '<', $tanggal)->orderBy('tanggal', 'desc')->first();

            if ($kemarin) {
                $detailsKemarinTunai = TransactionDetail::with('transaction')
                    ->whereDate('created_at', $kemarin->tanggal)
                    ->get()
                    ->filter(fn($d) => ($d->transaction->metode_pembayaran ?? 'Tunai') === 'Tunai');

                $masukKadoKemarin = $detailsKemarinTunai->where('pemilik', 'Kado Koeni')->sum('subtotal');
                $keluarKadoKemarin = Expense::whereDate('tanggal', $kemarin->tanggal)->where('pemilik', 'Kado Koeni')->sum('nominal');
                $sisaModalKadoKemarin = ($kemarin->modal_awal_kado + $masukKadoKemarin - $keluarKadoKemarin) - $kemarin->hasil_diambil_kado;

                $masukMitraKemarin = $detailsKemarinTunai->where('pemilik', 'Mitra Anda')->sum('subtotal');
                $keluarMitraKemarin = Expense::whereDate('tanggal', $kemarin->tanggal)->where('pemilik', 'Mitra Anda')->sum('nominal');
                $sisaModalMitraKemarin = ($kemarin->modal_awal_mitra + $masukMitraKemarin - $keluarMitraKemarin) - $kemarin->hasil_diambil_mitra;
            } else {
                $sisaModalKadoKemarin = 0;
                $sisaModalMitraKemarin = 0;
            }

            $cashflow = DailyCashflow::create([
                'tanggal' => $tanggal,
                'modal_awal_kado' => $sisaModalKadoKemarin,
                'modal_awal_mitra' => $sisaModalMitraKemarin,
                'hasil_diambil_kado' => 0,
                'hasil_diambil_mitra' => 0,
            ]);
        }

        // 5. Rumus Perhitungan Tabel Bawah (Murni Uang Tunai di Laci)
        $totalKado = $cashflow->modal_awal_kado + $pemasukanKado - $pengeluaranKado;
        $totalMitra = $cashflow->modal_awal_mitra + $pemasukanMitra - $pengeluaranMitra;
        $grandTotal = $totalKado + $totalMitra;

        $sisaModalKado = $totalKado - $cashflow->hasil_diambil_kado;
        $sisaModalMitra = $totalMitra - $cashflow->hasil_diambil_mitra;
        $totalSisaModal = $sisaModalKado + $sisaModalMitra;

        return view('laporan', compact(
            'tanggal', 'details', 'expenses', 'cashflow',
            'pemasukanKado', 'pemasukanMitra',
            'nonTunaiKado', 'nonTunaiMitra',
            'pengeluaranKado', 'pengeluaranMitra',
            'totalKado', 'totalMitra', 'grandTotal',
            'sisaModalKado', 'sisaModalMitra', 'totalSisaModal'
        ));
    }

    // Simpan Perubahan Modal Awal & Hasil yang Diambil
    public function simpanCashflow(Request $request)
    {
        $cashflow = DailyCashflow::where('tanggal', $request->tanggal)->firstOrFail();
        $cashflow->update([
            'modal_awal_kado' => $request->modal_awal_kado ?? 0,
            'modal_awal_mitra' => $request->modal_awal_mitra ?? 0,
            'hasil_diambil_kado' => $request->hasil_diambil_kado ?? 0,
            'hasil_diambil_mitra' => $request->hasil_diambil_mitra ?? 0,
        ]);

        return back()->with('success', 'Perhitungan Bagi Hasil & Sisa Modal berhasil disimpan!');
    }

    // Tambah Pengeluaran Harian (Misal: Laundry)
    public function tambahPengeluaran(Request $request)
    {
        Expense::create([
            'tanggal' => $request->tanggal,
            'pemilik' => $request->pemilik,
            'keterangan' => $request->keterangan,
            'nominal' => $request->nominal,
        ]);
        return back()->with('success', 'Catatan pengeluaran berhasil ditambahkan!');
    }

    // Hapus Pengeluaran
    public function hapusPengeluaran($id)
    {
        Expense::findOrFail($id)->delete();
        return back()->with('success', 'Pengeluaran dihapus!');
    }

    // Hapus Item Transaksi (Void) & Kembalikan Stok ke Gudang Otomatis
    public function hapusDetailTransaksi($id)
    {
        $detail = TransactionDetail::findOrFail($id);

        // 1. Kembalikan stok barang ke gudang jika tipenya 'Barang'
        if ($detail->product_id) {
            $product = Product::find($detail->product_id);
            if ($product && $product->tipe == 'Barang') {
                $product->stok = $product->stok + $detail->qty;
                $product->save();
            }
        }

        // 2. Perbarui atau hapus Nota Utama (Transaction)
        $trx = Transaction::find($detail->transaction_id);
        $detail->delete(); // Hapus rincian barang

        if ($trx) {
            // Cek apakah masih ada barang lain di nota yang sama
            $sisaBarang = TransactionDetail::where('transaction_id', $trx->id)->count();
            if ($sisaBarang == 0) {
                $trx->delete(); // Jika nota sudah kosong, hapus notanya sekalian
            } else {
                // Jika masih ada barang lain, kurangi total_bayar di nota utama
                $trx->total_bayar = TransactionDetail::where('transaction_id', $trx->id)->sum('subtotal');
                $trx->save();
            }
        }

        return back()->with('success', 'Item transaksi berhasil dibatalkan & stok gudang telah dikembalikan!');
    }

    // Export Laporan Cashflow Harian ke File Excel (.xls) Persis Format Asli
    public function exportExcel(Request $request)
    {
        $tanggal = $request->query('tanggal', date('Y-m-d'));

        $details = TransactionDetail::with('transaction')
                    ->whereDate('created_at', $tanggal)
                    ->latest()
                    ->get();

        $detailsTunai = $details->filter(fn($d) => ($d->transaction->metode_pembayaran ?? 'Tunai') === 'Tunai');
        $pemasukanKado = $detailsTunai->where('pemilik', 'Kado Koeni')->sum('subtotal');
        $pemasukanMitra = $detailsTunai->where('pemilik', 'Mitra Anda')->sum('subtotal');

        $expenses = Expense::whereDate('tanggal', $tanggal)->get();
        $pengeluaranKado = $expenses->where('pemilik', 'Kado Koeni')->sum('nominal');
        $pengeluaranMitra = $expenses->where('pemilik', 'Mitra Anda')->sum('nominal');

        $cashflow = DailyCashflow::where('tanggal', $tanggal)->first();
        $modalAwalKado = $cashflow ? $cashflow->modal_awal_kado : 0;
        $modalAwalMitra = $cashflow ? $cashflow->modal_awal_mitra : 0;
        $hasilKado = $cashflow ? $cashflow->hasil_diambil_kado : 0;
        $hasilMitra = $cashflow ? $cashflow->hasil_diambil_mitra : 0;

        $totalKado = $modalAwalKado + $pemasukanKado - $pengeluaranKado;
        $totalMitra = $modalAwalMitra + $pemasukanMitra - $pengeluaranMitra;
        $grandTotal = $totalKado + $totalMitra;

        $sisaModalKado = $totalKado - $hasilKado;
        $sisaModalMitra = $totalMitra - $hasilMitra;
        $totalSisaModal = $sisaModalKado + $sisaModalMitra;

        $namaFile = 'Cashflow_' . date('d_m_Y', strtotime($tanggal)) . '.xls';

        return response()->view('laporan_excel', compact(
            'tanggal', 'details', 'expenses',
            'modalAwalKado', 'modalAwalMitra',
            'pemasukanKado', 'pemasukanMitra',
            'pengeluaranKado', 'pengeluaranMitra',
            'totalKado', 'totalMitra', 'grandTotal',
            'hasilKado', 'hasilMitra',
            'sisaModalKado', 'sisaModalMitra', 'totalSisaModal'
        ))->header('Content-Type', 'application/vnd.ms-excel')
          ->header('Content-Disposition', 'attachment; filename="' . $namaFile . '"');
    }
}