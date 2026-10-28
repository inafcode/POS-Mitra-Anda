<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AdminController;

// Jika ada yang mengunjungi halaman utama (/), arahkan ke StoreController
Route::get('/', [StoreController::class, 'index']);

// Pintu Belakang (Kasir Toko Fisik)
Route::get('/kasir', [PosController::class, 'index']);

// Jalur rahasia untuk Barcode Scanner mencari data
Route::get('/api/search-product', [PosController::class, 'search']);

// Pintu Ruang Admin Gudang (CRUD)
Route::resource('/admin', AdminController::class);

// Jalur untuk mencetak struk kasir
Route::post('/kasir/cetak', [PosController::class, 'cetak']);

// Jalur Laporan Cashflow & Bagi Hasil
Route::get('/laporan', [PosController::class, 'laporan']);
Route::post('/laporan/cashflow', [PosController::class, 'simpanCashflow']);
Route::post('/laporan/pengeluaran', [PosController::class, 'tambahPengeluaran']);
Route::delete('/laporan/pengeluaran/{id}', [PosController::class, 'hapusPengeluaran']);