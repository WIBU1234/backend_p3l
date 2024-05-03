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
        CREATE TRIGGER update_bonus_null
        AFTER UPDATE ON tblPegawai
        FOR EACH ROW
        BEGIN
            DECLARE first_each_month DATE;
            SET first_each_month = DATE_FORMAT(NOW(), "%Y-%m-01");

            IF (NOW() = first_each_month) THEN
            UPDATE tblPegawai
                SET Bonus = NULL;
            END IF;
        END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_bonus_null');
    }
};
