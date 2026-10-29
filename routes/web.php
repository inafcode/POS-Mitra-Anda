<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InstansiController;

/*
|--------------------------------------------------------------------------
| Rute Publik & Autentikasi
|--------------------------------------------------------------------------
*/
Route::get('/', [AdminController::class, 'catalog'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rute Terproteksi (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 1. Dashboard Ringkasan
    |--------------------------------------------------------------------------
    */
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | 2. Katalog Barang (CRUD Gudang)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/produk')->name('admin.produk.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/create', [AdminController::class, 'create'])->name('create');
        Route::post('/', [AdminController::class, 'store'])->name('store');

        // RUTE BARU UNTUK IMPORT EXCEL
        Route::post('/import', [AdminController::class, 'importExcel'])->name('import');

        Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | 3. Kasir POS
    |--------------------------------------------------------------------------
    */
    Route::get('/kasir', [PosController::class, 'kasir'])->name('kasir.index');
    Route::get('/api/search-product', [PosController::class, 'search'])->name('kasir.search');
    Route::post('/kasir/cetak', [PosController::class, 'cetak'])->name('kasir.cetak');

    /*
    |--------------------------------------------------------------------------
    | 4. Buku Kas & Laporan Cashflow
    |--------------------------------------------------------------------------
    */
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [PosController::class, 'laporan'])->name('index');
        Route::post('/cashflow', [PosController::class, 'simpanCashflow'])->name('cashflow');
        Route::post('/pengeluaran', [PosController::class, 'tambahPengeluaran'])->name('pengeluaran.tambah');
        Route::delete('/pengeluaran/{id}', [PosController::class, 'hapusPengeluaran'])->name('pengeluaran.hapus');
        Route::delete('/detail/{id}', [PosController::class, 'hapusDetailTransaksi'])->name('detail.hapus');
        Route::get('/export', [PosController::class, 'exportExcel'])->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | 5. Mitra Instansi & Rekap Bon (Admin Panel)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/instansi')->name('admin.instansi.')->group(function () {
        Route::get('/', [InstansiController::class, 'index'])->name('index');
        Route::post('/', [InstansiController::class, 'store'])->name('store');
        Route::put('/{id}', [InstansiController::class, 'update'])->name('update');
        Route::delete('/{id}', [InstansiController::class, 'destroy'])->name('destroy');

        Route::get('/{id}/laporan', [InstansiController::class, 'laporanBon'])->name('laporan');
        Route::get('/{id}/export', [InstansiController::class, 'exportExcelBon'])->name('export');
        Route::post('/{id}/pelunasan-massal', [InstansiController::class, 'prosesPelunasanMassal'])->name('pelunasan');
        Route::get('/{id}/kwitansi', [InstansiController::class, 'cetakKwitansi'])->name('kwitansi');
    });

    // Aksi Transaksi Khusus Nota & Pelunasan Satuan
    Route::post('/admin/transaksi/{id}/lunasi', [InstansiController::class, 'lunasiBon'])->name('admin.transaksi.lunasi');
    Route::get('/admin/nota/instansi/{id}', [InstansiController::class, 'cetakNotaInstansi'])->name('admin.nota.instansi');

    /*
    |--------------------------------------------------------------------------
    | 6. Portal Mandiri Instansi (B2B Online Bon)
    |--------------------------------------------------------------------------
    */
    Route::prefix('portal-instansi')->name('portal.')->group(function () {
        Route::get('/', [InstansiController::class, 'portal'])->name('index');
        Route::post('/cart', [InstansiController::class, 'addToCart'])->name('cart.add');
        Route::delete('/cart/{id}', [InstansiController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/checkout', [InstansiController::class, 'checkoutCart'])->name('checkout');
    });

});