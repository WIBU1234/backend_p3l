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
        Schema::create('tblpresensi', function (Blueprint $table) {
            $table->integer('ID_Presensi')->autoIncrement();
            $table->integer('ID_Pegawai')->nullable();
            $table->date('Tanggal')->nullable();
            $table->string('Keterangan', 255)->nullable();

            $table->foreign('ID_Pegawai')->references('ID_Pegawai')->on('tblpegawai')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblpresensi');
    }
};
