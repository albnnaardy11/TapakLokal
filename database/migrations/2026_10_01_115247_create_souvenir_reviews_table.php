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
        Schema::create('souvenir_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('souvenir_order_item_id')->unique()->constrained();
            $table->foreignId('souvenir_product_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->unsignedTinyInteger('rating');
            $table->text('body');
            $table->text('vendor_response')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->index(['souvenir_product_id', 'status', 'id'], 'souvenir_review_public_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('souvenir_reviews');
    }
};
