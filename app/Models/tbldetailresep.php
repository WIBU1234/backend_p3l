<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbldetailresep extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tbldetailresep';
    protected $fillable = [
        "ID_Bahan_Baku",
        "ID_Produk",
        "Kuantitas",
    ];

    public function tblresep() {
        return $this->belongsTo(tblresep::class, 'ID_Produk', 'ID_Produk');
    }

    public function tblproduk() {
        return $this->belongsTo(tblproduk::class,'ID_Produk','ID_Produk');
    }

    public function tblbahanbaku() {
        return $this->belongsTo(tblbahanbaku::class, 'ID_Bahan_Baku', 'ID_Bahan_Baku');
    }
}
