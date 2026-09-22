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
        Schema::create('content_pages', function (Blueprint $t) {
        $t->id(); $t->string('type')->index(); $t->string('title'); $t->string('slug')->unique(); $t->text('excerpt')->nullable(); $t->longText('body'); $t->string('image_url', 2048)->nullable(); $t->string('status')->default('draft'); $t->timestamp('published_at')->nullable(); $t->unsignedInteger('position')->default(0); $t->timestamps(); $t->index(['type', 'status', 'published_at']);
        });
        Schema::create('faqs', function (Blueprint $t) { $t->id(); $t->string('category')->default('Umum'); $t->string('question'); $t->text('answer'); $t->string('status')->default('draft'); $t->unsignedInteger('position')->default(0); $t->timestamps(); });
        Schema::create('partners', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('image_url', 2048); $t->string('website_url', 2048)->nullable(); $t->string('status')->default('draft'); $t->unsignedInteger('position')->default(0); $t->timestamps(); });
        Schema::create('media_assets', function (Blueprint $t) { $t->id(); $t->foreignId('user_id')->constrained(); $t->string('name'); $t->string('disk'); $t->string('path'); $t->string('mime_type'); $t->unsignedBigInteger('size'); $t->string('alt_text')->nullable(); $t->string('visibility')->default('private'); $t->timestamps(); });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('content_pages');
    }
};
