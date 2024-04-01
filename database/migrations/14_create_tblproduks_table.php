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
        Schema::create('tblproduk', function (Blueprint $table) {
            $table->string('ID_Produk', 255)->primary();
            $table->integer('ID_Kategori')->nullable();
            $table->string('Nama_Produk', 255)->nullable();
            $table->integer('Harga')->nullable();
            $table->float('Stok')->nullable();
            $table->float('StokReady')->nullable();
            $table->string('Gambar', 255)->nullable();

            $table->foreign('ID_Kategori')->references('ID_Kategori')->on('tblkategori')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblproduk');
    }
};
