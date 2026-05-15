<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_provider')->nullable()->after('payment_status')->index();
            $table->string('payment_transaction_id')->nullable()->after('payment_provider')->index();
            $table->string('payment_reference')->nullable()->after('payment_transaction_id')->index();
            $table->json('payment_payload')->nullable()->after('payment_reference');
            $table->timestamp('paid_at')->nullable()->after('payment_payload');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'payment_transaction_id',
                'payment_reference',
                'payment_payload',
                'paid_at',
            ]);
        });
    }
};
