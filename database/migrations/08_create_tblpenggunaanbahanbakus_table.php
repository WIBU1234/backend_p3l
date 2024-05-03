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
        Schema::create('tblpenggunaanbahanbaku', function (Blueprint $table) {
            $table->integer('ID_Bahan_Baku')->nullable();
            $table->integer('Kuantitas')->nullable();
            $table->date('Tanggal')->nullable();

            $table->foreign('ID_Bahan_Baku')->references('ID_Bahan_Baku')->on('tblbahanbaku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblpenggunaanbahanbaku');
    }
};
