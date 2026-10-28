<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('nama_item');
            $table->string('pemilik'); // 'Mitra Anda' atau 'Kado Koeni'
            $table->integer('qty');
            $table->string('satuan')->nullable();
            $table->integer('harga_jual');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};