<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS daily_shift_reports");
        DB::statement("
            CREATE VIEW daily_shift_reports AS
            SELECT 
                date,
                SUM(CASE WHEN type = 'pemasukan' THEN 1 ELSE 0 END) as total_transaksi,
                SUM(CASE WHEN type = 'pemasukan' AND detail = 'tunai' THEN amount ELSE 0 END) as tunai_masuk,
                SUM(CASE WHEN type = 'pemasukan' AND detail = 'qris' THEN amount ELSE 0 END) as qris_masuk,
                SUM(CASE WHEN type = 'pengeluaran' THEN amount ELSE 0 END) as pengeluaran
            FROM transaction_ledgers
            GROUP BY date
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS daily_shift_reports");
    }
};
