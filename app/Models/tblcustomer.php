<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblcustomer extends Model
{
    use HasFactory;
    protected $table = 'tblCustomer';
    protected $primaryKey = 'ID_Customer';
    protected $fillable = [
        "Nama_Customer",
        "Email",
        "Password",
        "Nomor_Telepon",
        "Poin",
        "Saldo",
        "OTP",
        "Profile",
    ];
}
