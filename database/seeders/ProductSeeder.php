<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product; // Memanggil Kepala Gudang

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Memasukkan contoh Barang Fisik
        Product::create([
            'barcode' => 'MTA-0001',
            'nama_item' => 'Pulpen Faster Hitam',
            'tipe' => 'Barang',
            'kategori' => 'Alat Tulis',
            'satuan' => 'Pcs',
            'harga_modal' => 2000,
            'harga_jual_ecer' => 3000,
            'harga_jual_grosir' => 2500,
            'syarat_grosir' => 12,
            'stok' => 150,
        ]);

        // 2. Memasukkan contoh Layanan Jasa
        Product::create([
            'barcode' => 'JSA-PRT-01',
            'nama_item' => 'Print Warna Cover',
            'tipe' => 'Jasa',
            'kategori' => 'Printing',
            'satuan' => 'Lembar',
            'harga_modal' => 1000,
            'harga_jual_ecer' => 3000,
            'harga_jual_grosir' => 2500,
            'syarat_grosir' => 500,
            'stok' => null, // Jasa tidak punya stok
        ]);
    }
}