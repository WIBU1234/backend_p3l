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
        Schema::create('tblalamat', function (Blueprint $table) {
            $table->id('ID_Alamat');
            $table->integer('ID_Customer')->nullable();
            $table->string('Alamat', 255)->nullable();
            $table->integer('Jarak')->nullable();
            $table->integer('Biaya')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblalamat');
    }
};
