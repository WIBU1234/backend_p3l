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
            $table->integer('ID_Pegawai')->autoIncrement();
            $table->integer('ID_Jabatan')->nullable();
            $table->string('Nama_Pegawai', 255)->nullable();
            $table->string('Nomor_Rekening', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('Nomor_Telepon', 255)->nullable();
            $table->string('Gaji', 255)->nullable();
            $table->string('Bonus', 255)->nullable();
            $table->integer('OTP')->nullable();

            $table->foreign('ID_Jabatan')->references('ID_Jabatan')->on('tbljabatan')->onDelete('set null');
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
