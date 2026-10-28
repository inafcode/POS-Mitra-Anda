<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('instansi_pos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('instansi_id'); // Relasi ke tabel users (instansi)
        $table->string('nama_barang');
        $table->string('qty_satuan');
        $table->string('gambar')->nullable(); // Untuk menyimpan path foto referensi
        $table->text('catatan')->nullable();
        $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
        $table->timestamps();

        $table->foreign('instansi_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('instansi_pos');
    }
};
