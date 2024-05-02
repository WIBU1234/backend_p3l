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
        Schema::create('tbldetailresep', function (Blueprint $table) {
            $table->integer('ID_Bahan_Baku')->nullable();
            $table->string('ID_Produk', 255)->nullable();
            $table->integer('Kuantitas')->nullable();

            $table->foreign('ID_Bahan_Baku')->references('ID_Bahan_Baku')->on('tblbahanbaku')->onDelete('cascade');
            $table->foreign('ID_Produk')->references('ID_Produk')->on('tblresep')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbldetailresep');
    }
};
