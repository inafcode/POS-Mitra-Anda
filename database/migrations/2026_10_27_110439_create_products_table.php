<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('barcode')->unique()->nullable(); // (A) Kode Barcode
        $table->string('nama_item');                     // (B) Nama Barang/Jasa
        $table->enum('tipe', ['Barang', 'Jasa']);        // (C) Tipe Item
        $table->string('kategori');                      // (D) Kategori
        $table->string('satuan');                        // (E) Satuan (Pcs, Rim, Lembar)
        $table->integer('harga_modal')->nullable();      // (F) Harga Modal (HPP)
        $table->integer('harga_jual_ecer');              // (G) Harga Jual Ecer
        $table->integer('harga_jual_grosir')->nullable(); // (H) Harga Jual Grosir
        $table->integer('syarat_grosir')->nullable();    // (I) Syarat Grosir (Min. Beli)
        $table->integer('stok')->nullable();             // (J) Stok (Kosong untuk jasa)
        $table->timestamps();
    });
}
};
