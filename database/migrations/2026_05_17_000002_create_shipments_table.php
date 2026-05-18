<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->index();
            $table->string('provider_shipment_id')->nullable();
            $table->string('awb')->nullable();
            $table->string('shipment_status')->default('draft')->index();
            $table->string('shipment_status_code')->nullable()->index();
            $table->string('tracking_url')->nullable();
            $table->string('payment_mode')->index();
            $table->decimal('cod_amount', 12, 2)->default(0);
            $table->decimal('invoice_value', 12, 2)->default(0);
            $table->string('pickup_location_name')->nullable()->index();
            $table->string('serviceability_status')->nullable()->index();
            $table->string('label_path')->nullable();
            $table->timestamp('label_generated_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_shipment_id']);
            $table->unique(['provider', 'awb']);
            $table->index(['order_id', 'provider']);
            $table->index(['provider', 'shipment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
