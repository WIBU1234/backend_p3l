<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblhistorysaldo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tblhistorysaldo';
    protected $primaryKey = 'ID_History';
    protected $fillable = [
        "ID_Customer",
        "Tanggal",
        "Total",
    ];

    public function tblcustomer() {
        return $this->belongsTo(tblcustomer::class, 'ID_Customer', 'ID_Customer');
    }
}
