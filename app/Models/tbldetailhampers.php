<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbldetailhampers extends Model
{
    use HasFactory;
    protected $table = 'tblDetailHampers';
    protected $fillable = [
        "ID_Produk",
        "Hampers_ID_Produk",
        "Kuantitas",
    ];
    public function tblresep() {
        return $this->belongsTo(tblresep::class, 'ID_Produk', 'ID_Produk');
    }
    public function tblhampers() {
        return $this->belongsTo(tblhampers::class, 'Hampers_ID_Produk', 'ID_Produk');
    }
}
