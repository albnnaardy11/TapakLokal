<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_applications', function (Blueprint $table) {
            $table->id();
            $table->string('track', 20);
            $table->string('business', 120);
            $table->string('name', 100);
            $table->string('city', 100);
            $table->string('contact', 150);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('review_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_applications');
    }
};
