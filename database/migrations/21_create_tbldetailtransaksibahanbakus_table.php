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
        Schema::create('tbldetailtransaksibahanbaku', function (Blueprint $table) {
            $table->integer('ID_Bahan_Baku')->nullable();
            $table->integer('ID_transaksi_Baku')->nullable();
            $table->integer('Kuantitas')->nullable();
            $table->integer('Sub_Total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbldetailtransaksibahanbaku');
    }
};
