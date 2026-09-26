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
        Schema::create('virtual_tours', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->string('placement')->default('homepage');
            $table->foreignId('media_asset_id')->constrained()->restrictOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('content_page_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->string('source_hash', 64)->nullable()->unique();
            $table->index(['placement', 'status', 'position']);
            $table->index(['trip_id', 'status']);
            $table->index(['content_page_id', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_tours');
    }
};
