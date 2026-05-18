<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->index();
            $table->string('event_type')->index();
            $table->string('raw_status')->nullable();
            $table->string('normalized_status')->nullable()->index();
            $table->string('location')->nullable();
            $table->timestamp('event_time')->nullable()->index();
            $table->string('payload_hash', 64)->index();
            $table->json('raw_payload')->nullable();
            $table->boolean('is_duplicate')->default(false)->index();
            $table->timestamps();

            $table->unique(['shipment_id', 'provider', 'payload_hash']);
            $table->index(['shipment_id', 'event_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_events');
    }
};
