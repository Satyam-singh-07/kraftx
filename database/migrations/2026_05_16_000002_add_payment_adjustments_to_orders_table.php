<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('payment_fee_amount', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('payment_discount_amount', 12, 2)->default(0)->after('payment_fee_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_fee_amount', 'payment_discount_amount']);
        });
    }
};
