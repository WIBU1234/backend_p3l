<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblkategori extends Model
{
    use HasFactory;
    protected $table = 'tblkategori';
    protected $primaryKey = 'ID_Kategori';
    protected $fillable = [
        "ID_Kategori",
        "Nama_Kategori",
    ];
}
