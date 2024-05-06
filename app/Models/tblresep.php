<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class tblresep extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tblresep';
    protected $primaryKey = 'ID_Produk';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        "ID_Produk",
        "Waktu_Memproses",
    ];

    public function tblproduk() {
        return $this->belongsTo(tblproduk::class, 'ID_Produk', 'ID_Produk');
    }

    public function hampers(): BelongsToMany
    {
        return $this->belongsToMany(tblresep::class, 'tbldetailhamper', 'ID_Produk', 'Hampers_ID_Produk')
            ->withPivot('Kuantitas');
    }
}
