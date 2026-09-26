<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['content_pages', 'trips', 'faqs', 'partners', 'virtual_tours'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->softDeletes();
                $blueprint->string('seed_key')->nullable()->unique();
            });
        }
        Schema::table('content_pages', function (Blueprint $table) {
            $table->json('metadata')->nullable();
            $table->string('category')->nullable();
        });
        Schema::table('trips', fn (Blueprint $table) => $table->json('experience')->nullable());
    }

    public function down(): void
    {
        Schema::table('trips', fn (Blueprint $table) => $table->dropColumn('experience'));
        Schema::table('content_pages', fn (Blueprint $table) => $table->dropColumn(['metadata', 'category']));
        foreach (['content_pages', 'trips', 'faqs', 'partners', 'virtual_tours'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropSoftDeletes();
                $blueprint->dropColumn('seed_key');
            });
        }
    }
};
