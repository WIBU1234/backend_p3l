<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbltransaksi', function (Blueprint $table) {
            $table->string('ID_Transaksi', 255)->primary();
            $table->integer('ID_Customer');
            $table->integer('ID_Pegawai')->nullable();
            $table->integer('ID_Alamat');
            $table->dateTime('Tanggal_Transaksi')->nullable();
            $table->string('Status', 255)->nullable();
            $table->float('Total_Transaksi')->nullable();
            $table->date('Tanggal_Ambil')->nullable();
            $table->dateTime('Tanggal_Pelunasan')->nullable();
            $table->integer('Total_pembayaran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbltransaksi');
    }
};
