<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    // Mengizinkan Laravel mengisi kolom-kolom ini secara otomatis
    protected $fillable = [
        'barcode',
        'foto_produk',
        'nama_item',
        'tipe',
        'kategori',
        'satuan',
        'harga_modal',
        'harga_jual_ecer',
        'harga_jual_grosir',
        'syarat_grosir',
        'stok',
    ];
}