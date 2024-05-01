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
        Schema::create('tblhistorysaldo', function (Blueprint $table) {
            $table->integer('ID_History')->autoIncrement();
            $table->integer('ID_Customer')->nullable();
            $table->date('Tanggal')->nullable();
            $table->integer('Total')->nullable();

            $table->foreign('ID_Customer')->references('ID_Customer')->on('tblcustomer')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblhistorysaldo');
    }
};
