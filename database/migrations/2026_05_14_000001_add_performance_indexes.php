<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations — add performance indexes.
     */
    public function up(): void
    {
        // Orders: index on order_date (used in charts, reports, stats)
        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_date');
            $table->index('status');
            $table->index(['order_date', 'status']);
        });

        // Products: index on stock columns (used in stock alerts, sorting)
        Schema::table('products', function (Blueprint $table) {
            $table->index('stock');
            $table->index('category_id');
            $table->index(['stock', 'low_stock_threshold']);
        });

        // Order Items: index for joins (used in ROP chart, category sales)
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['order_date', 'status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['stock']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['stock', 'low_stock_threshold']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['order_id']);
        });
    }
};
