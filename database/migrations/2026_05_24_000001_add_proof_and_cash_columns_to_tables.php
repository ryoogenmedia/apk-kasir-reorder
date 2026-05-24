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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('qris_proof')->nullable()->after('payment_method');
            $table->decimal('amount_paid', 15, 2)->nullable()->after('total_amount');
            $table->decimal('change_amount', 15, 2)->nullable()->after('amount_paid');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->string('proof_image')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['qris_proof', 'amount_paid', 'change_amount']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('proof_image');
        });
    }
};
