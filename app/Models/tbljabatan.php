<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbljabatan extends Model
{
    use HasFactory;
    protected $table = 'tbljabatan';
    protected $primaryKey = 'ID_Jabatan';
    protected $fillable = [
        "Nama_Jabatan",
    ];

    public function pegawai()
    {
        return $this->hasMany(tblpegawai::class, 'ID_Jabatan', 'ID_Jabatan');
    }
}
