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
    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('instansi_id'); // Relasi ke users (instansi)
        $table->unsignedBigInteger('product_id'); // Relasi ke products
        $table->integer('qty');
        $table->timestamps();

        $table->foreign('instansi_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('carts');
}
};
