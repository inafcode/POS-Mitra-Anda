<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'kode_nota', 
        'metode_pembayaran',
        'total_bayar', 
        'uang_tunai', 
        'kembalian'];
    public function details() { return $this->hasMany(TransactionDetail::class); }
}