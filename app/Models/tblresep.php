<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblresep extends Model
{
    use HasFactory;
    protected $table = 'tblResep';
    protected $primaryKey = 'ID_Produk';
    protected $fillable = [
        "ID_Produk",
        "Waktu_Memproses",
    ];

    public function tblproduk() {
        return $this->belongsTo(tblproduk::class, 'ID_Produk', 'ID_Produk');
    }
}
