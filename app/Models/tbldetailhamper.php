<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbldetailhamper extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tbldetailhamper';
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
