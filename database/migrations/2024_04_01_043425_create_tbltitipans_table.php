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
        Schema::create('tbltitipan', function (Blueprint $table) {
            $table->string('ID_Produk', 255)->primary();
            $table->integer('ID_Penitip')->nullable();
            $table->integer('Harga_Beli')->nullable();
            $table->date('Tanggal_Stok')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbltitipan');
    }
};
