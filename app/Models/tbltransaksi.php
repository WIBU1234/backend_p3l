<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbltransaksi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tbltransaksi';
    protected $PrimaryKey = 'ID_Transaksi';
    protected $fillable = [
        "ID_Transaksi",
        "ID_Customer",
        "ID_Pegawai",
        "ID_Alamat",
        "Tanggal_Transaksi",
        "Status",
        "Total_Transaksi",
        "Tanggal_Ambil",
        "Total_Pembayaran",
    ];

    public function tblcustomer() {
        return $this->belongsTo(tblcustomer::class, 'ID_Customer', 'ID_Customer');
    }

    public function tblpegawai() {
        return $this->belongsTo(tblpegawai::class, 'ID_Pegawai', 'ID_Pegawai');
    }

    public function tblalamat() {
        return $this->belongsTo(tblalamat::class, 'ID_Alamat', 'ID_Alamat');
    }

    public function tbldetailtransaksi() {
        return $this->hasMany(tbldetailtransaksi::class, 'ID_Transaksi', 'ID_Transaksi');
    }
}
