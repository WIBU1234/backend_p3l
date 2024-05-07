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
    protected $table = 'tblcustomer';
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
        "OTP",
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function getRole(){
        return 'Customer';
    }

    public function tbltransaksi(){
        return $this->hasMany(tbltransaksi::class, 'ID_Customer');
    }

    public function tblalamat(){
        return $this->hasMany(tblalamat::class, 'ID_Customer');
    }
}
