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
        Schema::table('souvenir_products', function (Blueprint $table) {
            $table->index(['status', 'price', 'id'], 'souvenir_catalog_price_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('souvenir_products', function (Blueprint $table) {
            $table->dropIndex('souvenir_catalog_price_index');
        });
    }
};
