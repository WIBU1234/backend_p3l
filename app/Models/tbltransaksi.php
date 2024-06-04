<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class tbltransaksi extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'tbltransaksi';
    protected $primaryKey = 'ID_Transaksi';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        "ID_Transaksi",
        "ID_Customer",
        "ID_Pegawai",
        "ID_Alamat",
        "ID_JenisPengiriman",
        "Tanggal_Transaksi",
        "Status",
        "Total_Transaksi",
        "Tanggal_Ambil",
        "Total_Pembayaran",
        "Tip",
        "Bukti_Pembayaran",
        "Tipe_Transaksi",
        "Total_Bayar"
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

    public function tbljenispengiriman() {
        return $this->belongsTo(tbljenispengiriman::class, 'ID_JenisPengiriman', 'ID_JenisPengiriman');
    }

    public function products() : BelongsToMany 
    {
        return $this->belongsToMany(tblproduk::class, 'tbldetailtransaksi', 'ID_Transaksi', 'ID_Produk')
            ->withPivot('Kuantitas', 'Sub_Total', 'Tipe');
    }
}
