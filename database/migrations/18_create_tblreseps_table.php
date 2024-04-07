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
        Schema::create('tblresep', function (Blueprint $table) {
            $table->string('ID_Produk', 255)->primary();
            $table->integer('Waktu_Memproses')->nullable();

            $table->foreign('ID_Produk')->references('ID_Produk')->on('tblproduk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblresep');
    }
};
