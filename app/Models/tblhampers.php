<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class tblhampers extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tblHampers';
    protected $primaryKey = 'ID_Produk';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        "ID_Produk",
        "Kartu_Ucapan",
    ];

    public function tblproduk() 
    {
        return $this->belongsTo(tblproduk::class, 'ID_Produk', 'ID_Produk');
    }

    public function resep(): BelongsToMany
    {
        return $this->belongsToMany(tblresep::class, 'tbldetailhamper', 'Hampers_ID_Produk', 'ID_Produk')
            ->withPivot('Kuantitas');
    }
}