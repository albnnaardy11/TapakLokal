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
        Schema::create('payment_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('token', 64)->unique();
            $table->string('label', 100);
            $table->unsignedInteger('position');
            $table->unsignedBigInteger('amount');
            $table->string('status')->default('pending')->index();
            $table->string('method')->nullable();
            $table->json('instructions')->nullable();
            $table->string('provider_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->unique(['booking_id', 'position']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_shares');
    }
};
