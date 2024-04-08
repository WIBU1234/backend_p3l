<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblpenitip extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tblPenitip';
    protected $primaryKey = 'ID_Penitip';
    protected $fillable = [
        "Nama_Penitip",
    ];
}
