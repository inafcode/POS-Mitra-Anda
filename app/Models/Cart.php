<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Kolom yang diizinkan untuk diisi
    protected $fillable = [
        'instansi_id',
        'product_id',
        'qty',
    ];

    // Relasi: Satu item di keranjang ini milik satu Produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Relasi: Keranjang ini milik satu Instansi (User)
    public function instansi()
    {
        return $this->belongsTo(User::class, 'instansi_id');
    }
}