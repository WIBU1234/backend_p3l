<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
        CREATE TRIGGER insert_child_produk
        AFTER INSERT ON tblProduk
        FOR EACH ROW
        BEGIN
        IF (NEW.ID_Produk LIKE "PN%") THEN
            INSERT INTO tbltitipan VALUES (NEW.ID_Produk, NULL, NULL, NULL);
        ELSEIF (NEW.ID_Produk LIKE "AK%") THEN
            INSERT INTO tblresep VALUES (NEW.ID_Produk, NULL);
        ELSE
            INSERT INTO tblhampers VALUES (NEW.ID_Produk, NULL);
        END IF;
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_child_produk');
    }
};
