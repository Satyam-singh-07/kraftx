<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cart_items') && ! Schema::hasColumn('cart_items', 'linked_product_id')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->foreignId('linked_product_id')->nullable()->after('product_id')->constrained('products')->nullOnDelete();
            });
        }

        if (Schema::hasTable('order_items') && ! Schema::hasColumn('order_items', 'linked_product_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('linked_product_id')->nullable()->after('product_id')->constrained('products')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'linked_product_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['linked_product_id']);
                $table->dropColumn('linked_product_id');
            });
        }

        if (Schema::hasTable('cart_items') && Schema::hasColumn('cart_items', 'linked_product_id')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropForeign(['linked_product_id']);
                $table->dropColumn('linked_product_id');
            });
        }
    }
};
