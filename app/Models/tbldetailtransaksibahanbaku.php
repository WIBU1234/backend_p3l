<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbldetailtransaksibahanbaku extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tbldetailtransaksibahanbaku';
    protected $fillable = [
        "ID_transaksi_Baku",
        "ID_Bahan_Baku",
        "Kuantitas",
        "Sub_Total",
    ];

    public function tbltransaksibahanbaku() {
        return $this->belongsTo(tbltransaksibahanbaku::class, 'ID_Transaksi_Baku', 'ID_Transaksi_Baku');
    }

    public function tblbahanbaku() {
        return $this->belongsTo(tblbahanbaku::class, 'ID_Bahan_Baku', 'ID_Bahan_Baku');
    }
}
