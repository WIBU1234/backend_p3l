<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblpegawai extends Model
{
    use HasFactory;
    protected $table = 'tblPegawai';
    protected $primaryKey = 'ID_Pegawai';
    protected $fillable = [
        "ID_Jabatan",
        "Nama_Pegawai",
        "Nomor_Rekening",
        "Email",
        "Password",
        "Nomor_Telepon",
        "Gaji",
        "Bonus",
        "OTP",
    ];

    public function jabatan()
    {
        return $this->belongsTo(tbljabatan::class, 'ID_Jabatan', 'ID_Jabatan');
    }
}
