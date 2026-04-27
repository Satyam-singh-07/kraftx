<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight', 8, 3)->default(0)->after('price')->comment('Weight in Kg');
            $table->decimal('length', 8, 2)->default(0)->after('weight')->comment('Length in Cm');
            $table->decimal('width', 8, 2)->default(0)->after('length')->comment('Width in Cm');
            $table->decimal('height', 8, 2)->default(0)->after('width')->comment('Height in Cm');
            $table->string('hsn_code')->nullable()->after('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'length', 'width', 'height', 'hsn_code']);
        });
    }
};
