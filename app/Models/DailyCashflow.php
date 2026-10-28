<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DailyCashflow extends Model
{
    protected $fillable = ['tanggal', 'modal_awal_kado', 'modal_awal_mitra', 'hasil_diambil_kado', 'hasil_diambil_mitra'];
}