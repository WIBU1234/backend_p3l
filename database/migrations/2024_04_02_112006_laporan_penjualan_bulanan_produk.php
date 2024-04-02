<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('CREATE PROCEDURE laporan_penjualan_bulanan_produk(IN bulan INTEGER)
        BEGIN
            DECLARE namaProduk VARCHAR(255);
            DECLARE kuantitasProduk INTEGER;
            DECLARE hargaSatuan INTEGER;
            DECLARE subTotalProduk INTEGER;
            DECLARE notFound BOOLEAN;
            
            SELECT P.Nama_Produk, SUM(DT.Kuantitas) AS Kuantitas, P.Harga AS Harga, COUNT(DT.ID_Produk)*P.Harga AS Jumlah_Uang
                FROM tblProduk P 
                JOIN tblDetailTransaksi DT ON (P.ID_Produk = DT.ID_Produk)
                WHERE SUBSTR(DT.ID_Transaksi, 4, 2) = LPAD(bulan, 2, "0")
                GROUP BY P.Nama_Produk, P.Harga;
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS laporan_penjualan_bulanan_produk');
    }
};
