<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class InstansiController extends Controller
{
    // 1. Halaman Daftar Instansi (Di Ruang Admin)
    public function index()
    {
        $instansis = User::where('role', 'instansi')
                        ->with(['transactions' => function($q) {
                            $q->where('status_pembayaran', 'Belum Lunas');
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

    // 5. Halaman Laporan / Rekap Bon Instansi per Bulan (Fungsi yang hilang sebelumnya)
    public function laporanBon(Request $request, $id)
    {
        $instansi = User::where('role', 'instansi')->findOrFail($id);
        
        // Ambil filter bulan (format: YYYY-MM), default ke bulan sekarang
        $bulanPilih = $request->query('bulan', date('Y-m'));

        // Ambil transaksi HANYA pada bulan yang dipilih
        $transactions = Transaction::with('details')
                        ->where('instansi_id', $instansi->id)
                        ->where('metode_pembayaran', 'Bon / Tempo')
                        ->whereYear('created_at', substr($bulanPilih, 0, 4))
                        ->whereMonth('created_at', substr($bulanPilih, 5, 2))
                        ->latest()
                        ->get();

        // Total akumulasi seluruh tunggakan aktif yang belum lunas
        $totalHutangAktif = Transaction::where('instansi_id', $instansi->id)
                            ->where('metode_pembayaran', 'Bon / Tempo')
                            ->where('status_pembayaran', 'Belum Lunas')
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

    // 8. Pelunasan Massal Bon (Terikat pada bulan terpilih)
    public function prosesPelunasanMassal(Request $request, $id)
    {
        $notaDipilih = $request->input('nota_ids', []);
        $bulanPilih = $request->input('bulan', date('Y-m'));

        $transaksiBulanIni = Transaction::where('instansi_id', $id)
            ->where('metode_pembayaran', 'Bon / Tempo')
            ->whereYear('created_at', substr($bulanPilih, 0, 4))
            ->whereMonth('created_at', substr($bulanPilih, 5, 2))
            ->pluck('id')
            ->toArray();

        if (!empty($notaDipilih)) {
            Transaction::whereIn('id', $notaDipilih)
                ->update(['status_pembayaran' => 'Lunas']);
        }

        $notaTidakDicentang = array_diff($transaksiBulanIni, $notaDipilih);
        if (!empty($notaTidakDicentang)) {
            Transaction::whereIn('id', $notaTidakDicentang)
                ->update(['status_pembayaran' => 'Belum Lunas']);
        }

        // Redirect secara eksplisit ke url beserta bulannya agar halaman langsung terefresh dengan data baru
        return redirect("/admin/instansi/{$id}/laporan?bulan={$bulanPilih}")
            ->with('success', 'Status pembayaran bon periode ' . $bulanPilih . ' berhasil diperbarui!');
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
}