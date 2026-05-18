<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->index();
            $table->string('action')->default('create_shipment')->index();
            $table->string('idempotency_key', 120);
            $table->string('status')->default('pending')->index();
            $table->unsignedSmallInteger('attempt_count')->default(0);
            $table->text('last_error')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'idempotency_key']);
            $table->index(['order_id', 'provider', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_attempts');
    }
};
