<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblhampers extends Model
{
    use HasFactory;
    protected $table = 'tblHampers';
    protected $primaryKey = 'ID_Produk';
    protected $fillable = [
        "ID_Produk",
        "Kartu_Ucapan",
    ];

    public function tblproduk() {
        return $this->belongsTo(tblproduk::class, 'ID_Produk', 'ID_Produk');
    }
}
