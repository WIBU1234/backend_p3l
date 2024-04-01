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
        Schema::create('tbldetailhamper', function (Blueprint $table) {
            $table->string('ID_Produk', 255)->nullable();
            $table->string('Hampers_ID_Produk', 255)->nullable();
            $table->float('Kuantitas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbldetailhamper');
    }
};
