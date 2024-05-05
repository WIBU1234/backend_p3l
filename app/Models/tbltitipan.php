<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbltitipan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tbltitipan';
    protected $primaryKey = 'ID_Produk';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        "ID_Produk",
        "ID_Penitip",
        "Harga_Beli",
        "Tanggal_Stok",
    ];

    public function penitip()
    {
        return $this->belongsTo(tblpenitip::class, 'ID_Penitip', 'ID_Penitip');
    }

    public function tblproduk() {
        return $this->belongsTo(tblproduk::class, 'ID_Produk', 'ID_Produk');
    }
}
