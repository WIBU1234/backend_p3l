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
        DB::unprepared('
        CREATE TRIGGER insert_tgl_pembayaran
        AFTER INSERT ON tblTransaksi
        FOR EACH ROW
        BEGIN
        DECLARE tanggal_wajib_bayar DATETIME;
        SET tanggal_wajib_bayar = DATE_ADD(NEW.Tanggal_Transaksi, INTERVAL 1 DAY);
        
        SET NEW.Tanggal_Pelunasan = tanggal_wajib_bayar;
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_tgl_pembayaran');
    }
};
