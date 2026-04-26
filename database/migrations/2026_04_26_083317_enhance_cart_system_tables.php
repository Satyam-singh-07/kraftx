<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->string('status')->default('active')->index(); // active, abandoned, converted
            $table->timestamp('expires_at')->nullable();
            $table->string('device_type')->nullable(); // mobile, desktop
            $table->text('user_agent')->nullable();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            // Add unique constraint to prevent duplicates logic-wise
            $table->unique(['cart_id', 'product_id', 'product_variant_id'], 'cart_item_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_item_unique');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['status', 'expires_at', 'device_type', 'user_agent']);
        });
    }
};
