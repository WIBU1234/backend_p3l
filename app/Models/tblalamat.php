<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblalamat extends Model
{
    use HasFactory;
    protected $table = 'tblAlamat';
    protected $primaryKey = 'ID_Alamat';
    protected $fillable = [
        "ID_Customer",
        "Alamat",
        "Jarak",
        "Biaya",
    ];

    public function tblcustomer() {
        return $this->belongsTo(tblcustomer::class, 'ID_Customer', 'ID_Customer');
    }
}
