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
        DB::statement("DROP VIEW IF EXISTS transaction_ledgers");
        DB::statement("
            CREATE VIEW transaction_ledgers AS
            SELECT
                CONCAT('pemasukan_', o.id) AS id,
                'pemasukan' AS type,
                o.id AS reference_id,
                o.order_date AS date,
                o.total_amount AS amount,
                o.payment_method AS detail,
                u.name AS actor_name,
                o.created_at
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id

            UNION ALL

            SELECT
                CONCAT('pengeluaran_', p.id) AS id,
                'pengeluaran' AS type,
                p.id AS reference_id,
                p.purchase_date AS date,
                p.total_amount AS amount,
                s.name AS detail,
                u.name AS actor_name,
                p.created_at
            FROM purchases p
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            LEFT JOIN users u ON p.user_id = u.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS transaction_ledgers");
    }
};
