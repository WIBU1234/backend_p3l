<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbljabatan extends Model
{
    use HasFactory;
    protected $table = 'tblJabatan';
    protected $primaryKey = 'ID_Jabatan';
    protected $fillable = [
        "Nama_Jabatan",
    ];
}
