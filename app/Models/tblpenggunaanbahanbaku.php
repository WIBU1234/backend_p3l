<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblpenggunaanbahanbaku extends Model
{
    use HasFactory;
    protected $table = 'tblpenggunaanbahanbaku';
    protected $fillable = [
        "ID_Bahan_Baku",
        "Kuantitas",
        "Tanggal"
    ];

    public function tblbahanbaku() {
        return $this->belongsTo(tblbahanbaku::class, 'ID_Bahan_Baku', 'ID_Bahan_Baku');
    }
}
