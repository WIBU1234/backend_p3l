<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbljenispengiriman extends Model
{
    use HasFactory;
    protected $table = 'tbljenispengiriman';
    protected $primaryKey = 'ID_JenisPengiriman';
    protected $fillable = [
        "Nama_Pengiriman",
    ];
}
