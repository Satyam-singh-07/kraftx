<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('platform_order_id')->nullable()->after('shiprocket_order_id')->index();
            $table->string('fastrr_order_id')->nullable()->after('platform_order_id')->index();
            $table->string('cart_id')->nullable()->after('fastrr_order_id')->index();
            $table->string('checkout_status')->nullable()->after('status');
            $table->string('source')->nullable()->after('checkout_status');
            $table->string('shipping_plan')->nullable()->after('source');
            $table->string('rto_prediction')->nullable()->after('shipping_plan');
            $table->date('estimated_delivery_date')->nullable()->after('rto_prediction');
            $table->timestamp('shiprocket_order_created_at')->nullable()->after('estimated_delivery_date');
            $table->json('shipping_address_data')->nullable()->after('shipping_country');
            $table->json('billing_address_data')->nullable()->after('shipping_address_data');
            $table->json('payments')->nullable()->after('billing_address_data');
            $table->json('coupon_codes')->nullable()->after('payments');
            $table->json('discount_detail')->nullable()->after('coupon_codes');
            $table->json('shiprocket_tags')->nullable()->after('discount_detail');
            $table->json('shiprocket_payload')->nullable()->after('shiprocket_tags');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'platform_order_id',
                'fastrr_order_id',
                'cart_id',
                'checkout_status',
                'source',
                'shipping_plan',
                'rto_prediction',
                'estimated_delivery_date',
                'shiprocket_order_created_at',
                'shipping_address_data',
                'billing_address_data',
                'payments',
                'coupon_codes',
                'discount_detail',
                'shiprocket_tags',
                'shiprocket_payload',
            ]);
        });
    }
};
