<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reels', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('video_url'); // The link to the reel
            $table->string('thumbnail')->nullable(); // Optional thumbnail image
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); // Link to a product
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reels');
    }
};
