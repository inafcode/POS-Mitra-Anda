<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['tanggal', 'pemilik', 'keterangan', 'nominal'];
}