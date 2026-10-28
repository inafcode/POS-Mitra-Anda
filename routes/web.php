<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstansiController;

Route::get('/', [AdminController::class, 'catalog']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth'])->group(function () {
    
    // Halaman Kasir
    Route::get('/kasir', [PosController::class, 'kasir']);
    Route::get('/api/search-product', [PosController::class, 'search']);
    Route::post('/kasir/cetak', [PosController::class, 'cetak']);

    // Halaman Laporan Cashflow & Bagi Hasil
    Route::get('/laporan', [PosController::class, 'laporan']);
    Route::post('/laporan/cashflow', [PosController::class, 'simpanCashflow']);
    Route::post('/laporan/pengeluaran', [PosController::class, 'tambahPengeluaran']);
    Route::delete('/laporan/pengeluaran/{id}', [PosController::class, 'hapusPengeluaran']);
    Route::delete('/laporan/detail/{id}', [PosController::class, 'hapusDetailTransaksi']);
    Route::get('/laporan/export', [PosController::class, 'exportExcel']);

    // Halaman Gudang Admin (CRUD Produk)
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('/admin/create', [AdminController::class, 'create']);
    Route::post('/admin', [AdminController::class, 'store']);
    Route::get('/admin/{id}/edit', [AdminController::class, 'edit']);
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/{id}', [AdminController::class, 'destroy']);

    // Manajemen Mitra Instansi & Rekap Bon di Panel Admin (Tetap Ada)
    Route::get('/admin/instansi', [InstansiController::class, 'index']);
    Route::post('/admin/instansi', [InstansiController::class, 'store']);
    Route::put('/admin/instansi/{id}', [InstansiController::class, 'update']);
    Route::delete('/admin/instansi/{id}', [InstansiController::class, 'destroy']);

    Route::get('/admin/instansi/{id}/laporan', [InstansiController::class, 'laporanBon']);
    Route::post('/admin/transaksi/{id}/lunasi', [InstansiController::class, 'lunasiBon']);
    Route::get('/admin/instansi/{id}/export', [InstansiController::class, 'exportExcelBon']);
    Route::post('/admin/instansi/{id}/pelunasan-massal', [InstansiController::class, 'prosesPelunasanMassal']);
    Route::get('/admin/nota/instansi/{id}', [InstansiController::class, 'cetakNotaInstansi']);

    Route::get('/admin/instansi/{id}/kwitansi', [InstansiController::class, 'cetakKwitansi']);

    // ==========================================
    // PORTAL KHUSUS INSTANSI (B2B ONLINE BON)
    // ==========================================
    Route::get('/portal-instansi', [InstansiController::class, 'portal']);
    Route::post('/portal-instansi/cart', [InstansiController::class, 'addToCart']);
    Route::delete('/portal-instansi/cart/{id}', [InstansiController::class, 'removeFromCart']);
    Route::post('/portal-instansi/checkout', [InstansiController::class, 'checkoutCart']);
});