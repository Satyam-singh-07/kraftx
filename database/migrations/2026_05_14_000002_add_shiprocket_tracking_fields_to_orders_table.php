<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('awb_code')->nullable()->after('shiprocket_payload')->index();
            $table->string('courier_name')->nullable()->after('awb_code');
            $table->string('shipment_status')->nullable()->after('courier_name');
            $table->string('shipment_status_id')->nullable()->after('shipment_status');
            $table->timestamp('shipment_status_updated_at')->nullable()->after('shipment_status_id');
            $table->string('shipment_track_url')->nullable()->after('shipment_status_updated_at');
            $table->timestamp('delivered_at')->nullable()->after('shipment_track_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'awb_code',
                'courier_name',
                'shipment_status',
                'shipment_status_id',
                'shipment_status_updated_at',
                'shipment_track_url',
                'delivered_at',
            ]);
        });
    }
};
