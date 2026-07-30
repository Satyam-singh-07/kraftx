<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_variants') || Schema::hasColumn('product_variants', 'image_paths')) {
            return;
        }

        Schema::table('product_variants', function (Blueprint $table) {
            $table->json('image_paths')->nullable()->after('sku');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('product_variants', 'image_paths')) {
            return;
        }

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('image_paths');
        });
    }
};
