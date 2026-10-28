<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom Role & Info Instansi di tabel users
        Schema::table('users', function (Blueprint $table) {$table->string('role')->default('admin')->after('email'); // 'admin' atau 'instansi'
            $table->string('no_hp')->nullable()->after('role');$table->text('alamat')->nullable()->after('no_hp');
        });

        // 2. Tambah kolom Relasi Instansi & Status Bon di tabel transactions
        Schema::table('transactions', function (Blueprint $table) {$table->foreignId('instansi_id')->nullable()->after('kode_nota')->constrained('users')->nullOnDelete();
            $table->string('status_pembayaran')->default('Lunas')->after('metode_pembayaran'); // 'Lunas' atau 'Belum Lunas'$table->timestamp('tanggal_lunas')->nullable()->after('status_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['instansi_id']);$table->dropColumn(['instansi_id', 'status_pembayaran', 'tanggal_lunas']);
        });

        Schema::table('users', function (Blueprint $table) {$table->dropColumn(['role', 'no_hp', 'alamat']);
        });
    }
};