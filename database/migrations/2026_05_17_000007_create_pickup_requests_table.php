<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->index();
            $table->string('pickup_request_id')->nullable();
            $table->string('pickup_location_name')->index();
            $table->date('scheduled_date')->nullable()->index();
            $table->string('status')->default('draft')->index();
            $table->unsignedInteger('shipment_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'pickup_request_id']);
            $table->index(['provider', 'status']);
        });

        Schema::create('pickup_request_shipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['pickup_request_id', 'shipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_request_shipment');
        Schema::dropIfExists('pickup_requests');
    }
};
