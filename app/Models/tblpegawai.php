<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class tblpegawai extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = 'tblPegawai';
    protected $primaryKey = 'ID_Pegawai';
    protected $fillable = [
        "ID_Jabatan",
        "Nama_Pegawai",
        "Nomor_Rekening",
        "email",
        "password",
        "Nomor_Telepon",
        "Gaji",
        "Bonus",
        "OTP",
    ];

    protected $hidden = [
        "password",
        "OTP",
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function jabatan()
    {
        return $this->belongsTo(tbljabatan::class, 'ID_Jabatan', 'ID_Jabatan');
    }
}
