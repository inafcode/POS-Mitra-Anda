<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = ['transaction_id', 'product_id', 'nama_item', 'pemilik', 'qty', 'satuan', 'harga_jual', 'subtotal'];
    public function transaction() { return $this->belongsTo(Transaction::class); }
}