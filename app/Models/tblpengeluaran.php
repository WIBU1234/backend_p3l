<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblpengeluaran extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tblpengeluaran';
    protected $primaryKey = 'ID_Pengeluaran';
    protected $fillable = [
        "ID_Pengeluaran",
        "Nama",
        "Harga",
        "Tanggal",
    ];

    public function getKey()
    {
        return null;
    }
}
