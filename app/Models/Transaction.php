<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'kode_nota',
        'instansi_id',
        'metode_pembayaran',
        'status_pembayaran',
        'tanggal_lunas',
        'total_bayar',
        'uang_tunai',
        'kembalian',
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Relasi: Nota ini dimiliki oleh Instansi mana (jika transaksi instansi)
    public function instansi()
    {
        return $this->belongsTo(User::class, 'instansi_id');
    }
}