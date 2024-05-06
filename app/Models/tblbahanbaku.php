<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblbahanbaku extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tblbahanbaku';
    protected $primaryKey = 'ID_Bahan_Baku';
    protected $fillable = [
        "Nama_Bahan",
        "Stok",
        "Satuan",
    ];
}
