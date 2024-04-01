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
        Schema::create('tblbahanbaku', function (Blueprint $table) {
            $table->id('ID_Bahan_Baku');
            $table->string('Nama_Bahan', 255)->nullable();
            $table->integer('Stok')->nullable();
            $table->string('Satuan', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblbahanbaku');
    }
};
