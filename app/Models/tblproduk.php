<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblproduk extends Model
{
    use HasFactory;
    protected $table = 'tblProduk';
    protected $primaryKey = 'ID_Produk';
    protected $fillable = [
        "ID_Kategori",
        "Nama_Produk",
        "Harga",
        "Stok",
        "StokReady",
        "Gambar",
    ];

    public function kategori()
    {
        return $this->belongsTo(tblkategori::class, 'ID_Kategori', 'ID_Kategori');
    }
}
