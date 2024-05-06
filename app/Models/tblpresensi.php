<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblpresensi extends Model
{
    use HasFactory;
    protected $table = 'tblpresensi';
    protected $primaryKey = 'ID_Presensi';
    protected $fillable = [
        "ID_Pegawai",
        "Tanggal",
        "Keterangan",
    ];

    public function pegawai()
    {
        return $this->belongsTo(tblpegawai::class, 'ID_Pegawai', 'ID_Pegawai');
    }
}
