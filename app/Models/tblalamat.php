<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblalamat extends Model
{
    use HasFactory;
    protected $table = 'tblalamat';
    protected $primaryKey = 'ID_Alamat';
    public $timestamps = false;
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
