<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Expense;
use App\Models\DailyCashflow;
use App\Models\User;
use Carbon\Carbon;

class PosController extends Controller
{
    public function kasir()
    {
        $instansis = User::where('role', 'instansi')->get();

        // Ambil semua produk yang stoknya masih tersedia untuk etalase samping
        $products = Product::where('stok', '>', 0)
                           ->orderBy('nama_item', 'asc')
                           ->get();

        return view('kasir', compact('instansis', 'products'));
    }

    // Fungsi API Live Search untuk mencari Barang & Jasa di Kasir
    public function search(Request $request)
    {
        $keyword = $request->query('q');

        if (!$keyword) {
            return response()->json([]);
        }

        $exactBarcode = Product::where('barcode', $keyword)->first();
        if ($exactBarcode) {
            return response()->json([$exactBarcode]);
        }

        $products = Product::where('nama_item', 'like', '%' . $keyword . '%')
                    ->orWhere('barcode', 'like', '%' . $keyword . '%')
                    ->orderBy('stok', 'desc')
                    ->limit(10)
                    ->get();

        return response()->json($products);
    }

    // Fungsi cetak struk + simpan riwayat transaksi + potong stok + dukung Bon Kasir Manual
    public function cetak(Request $request)
    {
        $cartData = json_decode($request->cart_data, true);
        $totalBayar = $request->total_bayar;
        $uangTunai = $request->uang_tunai;
        $kembalian = $request->kembalian;
        $metodePembayaran = $request->metode_pembayaran ?? 'Tunai';
        $instansiId = $request->instansi_id ?: null;

        // --- BAGIAN YANG DIUBAH (Logika Status Pembayaran) ---
        if ($instansiId && $metodePembayaran === 'Tunai') {
            $statusPembayaran = 'Menunggu Pembayaran'; // Menunggu ACC kasir
        } else {
            $statusPembayaran = ($metodePembayaran === 'Bon / Tempo') ? 'Belum Lunas' : 'Lunas';
        }
        // -----------------------------------------------------

        $nextId = (Transaction::max('id') ?? 0) + 1;
        $kodeNota = '#' . (4800 + $nextId);

        $trx = Transaction::create([
            'kode_nota' => $kodeNota,
            'instansi_id' => $instansiId,
            'metode_pembayaran' => $metodePembayaran,
            'status_pembayaran' => $statusPembayaran,
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
                // --- BAGIAN YANG DIPERBAIKI (Tanda kurang menyatu) ---
                $product->stok = $product->stok - $item['qty'];
                $product->save();
            }
        }
        
        return view('struk', compact('cartData', 'totalBayar', 'uangTunai', 'kembalian', 'kodeNota', 'metodePembayaran', 'statusPembayaran'));
    }

    // Mengambil data detail transaksi gabungan (Kasir & Pelunasan Instansi)
    private function getLaporanDetails($tanggal)
    {
        // 1. Transaksi reguler & instansi yang lunas di hari yang sama
        $detailsHariIni = TransactionDetail::with('transaction')
            ->whereHas('transaction', function($q) {
                $q->where('metode_pembayaran', '!=', 'Bon / Tempo');
            })
            ->whereDate('created_at', $tanggal)
            ->get();

        // 2. Pelunasan Bon Instansi (Transaksi dibuat di masa lalu, tapi dilunasi HARI INI secara Tunai)
        $pelunasanInstansi = TransactionDetail::with('transaction')
            ->whereHas('transaction', function($q) use ($tanggal) {
                $q->whereNotNull('instansi_id')
                  ->where('status_pembayaran', 'Lunas')
                  ->where('metode_pembayaran', 'Tunai') // Masuk ke laci jika lunas tunai
                  ->whereDate('updated_at', $tanggal)
                  ->whereDate('created_at', '!=', $tanggal); // Hindari duplikasi
            })
            ->get();

        // Tandai pelunasan instansi dan sesuaikan waktu laporannya menjadi waktu pelunasan (updated_at)
        foreach ($pelunasanInstansi as $detail) {
            $detail->dari_instansi = true;
            $detail->created_at = $detail->transaction->updated_at;
        }

        // Gabungkan dan urutkan dari yang terbaru
        return $detailsHariIni->concat($pelunasanInstansi)->sortByDesc('created_at')->values();
    }

    // Halaman Laporan Cashflow
    public function laporan(Request $request)
    {
        $tanggal = $request->query('tanggal', date('Y-m-d'));

        // Gunakan fungsi private untuk mengambil data gabungan
        $details = $this->getLaporanDetails($tanggal);

        $detailsTunai = $details->filter(fn($d) => ($d->transaction->metode_pembayaran ?? 'Tunai') === 'Tunai');
        $pemasukanKado = $detailsTunai->where('pemilik', 'Kado Koeni')->sum('subtotal');
        $pemasukanMitra = $detailsTunai->where('pemilik', 'Mitra Anda')->sum('subtotal');

        $detailsNonTunai = $details->filter(fn($d) => ($d->transaction->metode_pembayaran ?? 'Tunai') !== 'Tunai');
        $nonTunaiKado = $detailsNonTunai->where('pemilik', 'Kado Koeni')->sum('subtotal');
        $nonTunaiMitra = $detailsNonTunai->where('pemilik', 'Mitra Anda')->sum('subtotal');

        $expenses = Expense::whereDate('tanggal', $tanggal)->get();
        $pengeluaranKado = $expenses->where('pemilik', 'Kado Koeni')->sum('nominal');
        $pengeluaranMitra = $expenses->where('pemilik', 'Mitra Anda')->sum('nominal');

        $cashflow = DailyCashflow::where('tanggal', $tanggal)->first();

        if (!$cashflow) {
            $kemarin = DailyCashflow::where('tanggal', '<', $tanggal)->orderBy('tanggal', 'desc')->first();

            if ($kemarin) {
                $detailsKemarinTunai = $this->getLaporanDetails($kemarin->tanggal)
                                            ->filter(fn($d) => ($d->transaction->metode_pembayaran ?? 'Tunai') === 'Tunai');

                $masukKadoKemarin = $detailsKemarinTunai->where('pemilik', 'Kado Koeni')->sum('subtotal');
                // PERBAIKAN: Laci menginap tidak dikurangi pengeluaran, karena pengeluaran dibayar dari dompet
                $sisaModalKadoKemarin = $kemarin->modal_awal_kado + $masukKadoKemarin - $kemarin->hasil_diambil_kado;

                $masukMitraKemarin = $detailsKemarinTunai->where('pemilik', 'Mitra Anda')->sum('subtotal');
                // PERBAIKAN: Laci menginap tidak dikurangi pengeluaran
                $sisaModalMitraKemarin = $kemarin->modal_awal_mitra + $masukMitraKemarin - $kemarin->hasil_diambil_mitra;
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
                'tarik_dadakan_kado' => 0,   // Pastikan ini ada di database
                'tarik_dadakan_mitra' => 0,  // Pastikan ini ada di database
            ]);
        }

        // 1. HITUNG LACI BERSAMA (Hanya Modal + Penjualan)
        $totalLaciKado = $cashflow->modal_awal_kado + $pemasukanKado;
        $totalLaciMitra = $cashflow->modal_awal_mitra + $pemasukanMitra;
        $grandTotalLaci = $totalLaciKado + $totalLaciMitra;

        // 2. HITUNG SISA LACI MENGINAP (Total Laci dikurangi Bagi Hasil yang ditarik ke Dompet)
        $sisaModalKado = $totalLaciKado - $cashflow->hasil_diambil_kado;
        $sisaModalMitra = $totalLaciMitra - $cashflow->hasil_diambil_mitra;
        $totalSisaModal = $sisaModalKado + $sisaModalMitra;

        // 3. HITUNG DOMPET PRIBADI (Bagi Hasil ditarik - Pengeluaran - Tarik Dadakan)
        $sisaDompetKado = $cashflow->hasil_diambil_kado - $pengeluaranKado - ($cashflow->tarik_dadakan_kado ?? 0);
        $sisaDompetMitra = $cashflow->hasil_diambil_mitra - $pengeluaranMitra - ($cashflow->tarik_dadakan_mitra ?? 0);

        return view('laporan', compact(
            'tanggal', 'details', 'expenses', 'cashflow',
            'pemasukanKado', 'pemasukanMitra',
            'nonTunaiKado', 'nonTunaiMitra',
            'pengeluaranKado', 'pengeluaranMitra',
            'totalLaciKado', 'totalLaciMitra', 'grandTotalLaci', 
            'sisaModalKado', 'sisaModalMitra', 'totalSisaModal',
            'sisaDompetKado', 'sisaDompetMitra' 
        ));
    }

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

    public function hapusPengeluaran($id)
    {
        Expense::findOrFail($id)->delete();
        return back()->with('success', 'Pengeluaran dihapus!');
    }

    public function hapusDetailTransaksi($id)
    {
        $detail = TransactionDetail::findOrFail($id);

        if ($detail->product_id) {
            $product = Product::find($detail->product_id);
            if ($product && $product->tipe == 'Barang') {
                $product->stok = $product->stok + $detail->qty;
                $product->save();
            }
        }

        $trx = Transaction::find($detail->transaction_id);
        $detail->delete();

        if ($trx) {
            $sisaBarang = TransactionDetail::where('transaction_id', $trx->id)->count();
            if ($sisaBarang == 0) {
                $trx->delete();
            } else {
                $trx->total_bayar = TransactionDetail::where('transaction_id', $trx->id)->sum('subtotal');
                $trx->save();
            }
        }

        return back()->with('success', 'Item transaksi berhasil dibatalkan & stok gudang telah dikembalikan!');
    }

    public function exportExcel(Request $request)
    {
        $tanggal = $request->query('tanggal', date('Y-m-d'));

        // Gunakan fungsi private agar perhitungan Excel sama persis dengan UI
        $details = $this->getLaporanDetails($tanggal);

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

        // 1. HITUNG LACI BERSAMA (Hanya Modal + Penjualan) - DISAMAKAN DENGAN LOGIKA BARU
        $totalLaciKado = $modalAwalKado + $pemasukanKado;
        $totalLaciMitra = $modalAwalMitra + $pemasukanMitra;
        $grandTotalLaci = $totalLaciKado + $totalLaciMitra;

        // 2. HITUNG SISA LACI MENGINAP (Total Laci - Bagi Hasil)
        $sisaModalKado = $totalLaciKado - $hasilKado;
        $sisaModalMitra = $totalLaciMitra - $hasilMitra;
        $totalSisaModal = $sisaModalKado + $sisaModalMitra;

        // 3. HITUNG DOMPET PRIBADI (Bagi Hasil - Pengeluaran - Tarik Dadakan)
        $sisaDompetKado = $hasilKado - $pengeluaranKado - ($cashflow->tarik_dadakan_kado ?? 0);
        $sisaDompetMitra = $hasilMitra - $pengeluaranMitra - ($cashflow->tarik_dadakan_mitra ?? 0);

        $namaFile = 'Cashflow_' . date('d_m_Y', strtotime($tanggal)) . '.xls';

        return response()->view('laporan_excel', compact(
            'tanggal', 'details', 'expenses',
            'modalAwalKado', 'modalAwalMitra',
            'pemasukanKado', 'pemasukanMitra',
            'pengeluaranKado', 'pengeluaranMitra',
            'totalLaciKado', 'totalLaciMitra', 'grandTotalLaci', // Disesuaikan nama variabelnya
            'hasilKado', 'hasilMitra',
            'sisaModalKado', 'sisaModalMitra', 'totalSisaModal',
            'sisaDompetKado', 'sisaDompetMitra' // Variabel baru dikirim
        ))->header('Content-Type', 'application/vnd.ms-excel')
          ->header('Content-Disposition', 'attachment; filename="' . $namaFile . '"');
    }

    // Menampilkan halaman daftar pesanan instansi yang belum lunas
    public function pesananInstansi()
    {
        // Ambil transaksi instansi yang statusnya butuh ACC (Belum Lunas / Menunggu Pembayaran)
        $pesanan = Transaction::with(['details']) 
                    ->whereNotNull('instansi_id')
                    ->whereIn('status_pembayaran', ['Belum Lunas', 'Menunggu Pembayaran'])
                    ->latest()
                    ->get();

        return view('admin.pesanan', compact('pesanan'));
    }

    // Memproses ACC pembayaran tunai/transfer menjadi Lunas
    public function accPesananInstansi($id)
    {
        $transaksi = Transaction::findOrFail($id);
        
        $transaksi->update([
            'status_pembayaran' => 'Lunas'
        ]);

        return back()->with('success', 'Pesanan ' . $transaksi->kode_nota . ' berhasil di-ACC. Dana telah diteruskan ke kalkulasi Buku Kas.');
    }
}