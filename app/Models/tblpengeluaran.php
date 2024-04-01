<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblpengeluaran extends Model
{
    use HasFactory;
    protected $table = 'tblPengeluaran';
    protected $fillable = [
        "Nama",
        "Harga",
        "Tanggal",
    ];
}
