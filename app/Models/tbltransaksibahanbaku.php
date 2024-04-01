<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbltransaksibahanbaku extends Model
{
    use HasFactory;
    protected $table = 'tblTransaksiBahanBaku';
    protected $PrimaryKey = 'ID_Transaksi_Baku';
    protected $fillable = [
        "Tanggal",
    ];
}
