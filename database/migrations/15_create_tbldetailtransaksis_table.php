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
        Schema::create('tbldetailtransaksi', function (Blueprint $table) {
            $table->string('ID_Produk', 255)->nullable();
            $table->string('ID_Transaksi', 255)->nullable();
            $table->float('Kuantitas')->nullable();
            $table->integer('Sub_Total')->nullable();

            $table->foreign('ID_Produk')->references('ID_Produk')->on('tblproduk');
            $table->foreign('ID_Transaksi')->references('ID_Transaksi')->on('tbltransaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbldetailtransaksi');
    }
};
