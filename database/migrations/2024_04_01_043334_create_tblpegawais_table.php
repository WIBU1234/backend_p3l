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
        Schema::create('tblpegawai', function (Blueprint $table) {
            $table->id('ID_Pegawai');
            $table->integer('ID_Jabatan')->nullable();
            $table->string('Nama_Pegawai', 255)->nullable();
            $table->string('Nomor_Rekening', 255)->nullable();
            $table->string('Email', 255)->nullable();
            $table->string('Password', 255)->nullable();
            $table->string('Nomor_Telepon', 255)->nullable();
            $table->string('Gaji', 255)->nullable();
            $table->string('Bonus', 255)->nullable();
            $table->integer('OTP')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblpegawai');
    }
};
