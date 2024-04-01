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
        Schema::create('tblcustomer', function (Blueprint $table) {
            $table->integer('ID_Customer')->autoIncrement();
            $table->string('Nama_Customer', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('Nomor_telepon', 255)->nullable();
            $table->integer('Poin')->nullable();
            $table->integer('Saldo')->nullable();
            $table->integer('OTP')->nullable();
            $table->string('Profile', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblcustomer');
    }
};
