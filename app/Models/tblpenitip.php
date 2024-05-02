<?php

namespace App\Models;

use App\Models\tbltitipan;
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

    public function titipan(){
        return $this->hasMany(tbltitipan::class, 'ID_Penitip');
    }
}
