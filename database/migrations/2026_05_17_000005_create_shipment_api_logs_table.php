<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->index();
            $table->string('endpoint');
            $table->string('request_type', 16);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->boolean('success')->default(false)->index();
            $table->json('request_summary')->nullable();
            $table->json('response_summary')->nullable();
            $table->unsignedSmallInteger('retry_count')->default(0);
            $table->timestamps();

            $table->index(['provider', 'endpoint']);
            $table->index(['shipment_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_api_logs');
    }
};
