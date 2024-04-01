<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbldetailtransaksi extends Model
{
    use HasFactory;
    protected $table = 'tblDetailTransaksi';
    protected $fillable = [
        "ID_Transaksi",
        "ID_Produk",
        "Kuantitas",
        "Sub_Total",
    ];

    public function tbltransaksi() {
        return $this->belongsTo(tbltransaksi::class, 'ID_Transaksi', 'ID_Transaksi');
    }

    public function tblproduk() {
        return $this->belongsTo(tblproduk::class, 'ID_Produk', 'ID_Produk');
    }
}
