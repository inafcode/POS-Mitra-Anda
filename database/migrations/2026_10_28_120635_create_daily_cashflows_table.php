<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_cashflows', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->integer('modal_awal_kado')->default(0);
            $table->integer('modal_awal_mitra')->default(0);
            $table->integer('hasil_diambil_kado')->default(0);
            $table->integer('hasil_diambil_mitra')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_cashflows');
    }
};