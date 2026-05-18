<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('package_number')->default(1);
            $table->decimal('weight_kg', 10, 3);
            $table->decimal('length_cm', 10, 2);
            $table->decimal('width_cm', 10, 2);
            $table->decimal('height_cm', 10, 2);
            $table->decimal('volumetric_weight_kg', 10, 3)->default(0);
            $table->string('awb')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['shipment_id', 'package_number']);
            $table->unique(['shipment_id', 'awb']);
            $table->index('awb');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_packages');
    }
};
