<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class tblcustomer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    public $timestamps = false;
    protected $table = 'tblCustomer';
    protected $primaryKey = 'ID_Customer';
    protected $fillable = [
        "Nama_Customer",
        "email",
        "password",
        "Nomor_telepon",
        "Poin",
        "Saldo",
        "OTP",
        "Profile",
    ];

    protected $hidden = [
        "password",
        "OTP",
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
}
