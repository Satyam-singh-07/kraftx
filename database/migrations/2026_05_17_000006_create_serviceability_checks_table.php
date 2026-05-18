<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serviceability_checks', function (Blueprint $table) {
            $table->id();
            $table->string('pincode', 6);
            $table->string('provider')->index();
            $table->boolean('is_serviceable')->default(false)->index();
            $table->boolean('cod_available')->nullable();
            $table->boolean('prepaid_available')->nullable();
            $table->unsignedSmallInteger('estimated_days')->nullable();
            $table->json('response_snapshot')->nullable();
            $table->timestamp('checked_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['provider', 'pincode']);
            $table->index(['pincode', 'provider', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serviceability_checks');
    }
};
