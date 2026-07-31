<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_variants')) {
            return;
        }

        if (! Schema::hasColumn('product_variants', 'items_count')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->unsignedInteger('items_count')->default(1)->after('color');
            });
        }

        if (! Schema::hasColumn('product_variants', 'linked_skus')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->json('linked_skus')->nullable()->after('items_count');
            });
        }

        if (Schema::hasColumn('product_variants', 'image_paths')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('image_paths');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_variants')) {
            return;
        }

        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'linked_skus')) {
                $table->dropColumn('linked_skus');
            }

            if (Schema::hasColumn('product_variants', 'items_count')) {
                $table->dropColumn('items_count');
            }
        });
    }
};
