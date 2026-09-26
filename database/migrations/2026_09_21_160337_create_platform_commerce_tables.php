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
        Schema::create('vendors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained();
            $t->string('name');
            $t->string('city');
            $t->string('email');
            $t->string('phone', 30);
            $t->text('description')->nullable();
            $t->string('status')->default('pending')->index();
            $t->foreignId('document_id')->nullable()->constrained('media_assets');
            $t->text('verification_note')->nullable();
            $t->timestamps();
        });
        Schema::create('trips', function (Blueprint $t) {
            $t->id();
            $t->foreignId('vendor_id')->constrained();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('type');
            $t->string('destination');
            $t->text('description');
            $t->text('itinerary');
            $t->string('meeting_point');
            $t->string('image_url', 2048)->nullable();
            $t->date('departure_date');
            $t->date('end_date');
            $t->unsignedInteger('capacity');
            $t->unsignedInteger('reserved_seats')->default(0);
            $t->unsignedBigInteger('price');
            $t->string('status')->default('draft');
            $t->timestamps();
            $t->index(['status', 'departure_date']);
            $t->index(['vendor_id', 'status']);
        });
        Schema::create('promotions', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('code')->unique();
            $t->string('type');
            $t->unsignedInteger('value');
            $t->unsignedBigInteger('minimum_amount')->default(0);
            $t->unsignedBigInteger('maximum_discount')->nullable();
            $t->unsignedInteger('usage_limit')->default(100);
            $t->unsignedInteger('used_count')->default(0);
            $t->date('starts_at');
            $t->date('ends_at');
            $t->string('status')->default('draft');
            $t->timestamps();
        });
        Schema::create('bookings', function (Blueprint $t) {
            $t->unsignedBigInteger('platform_fee')->default(0);
            $t->unsignedBigInteger('vendor_amount')->default(0);
            $t->id();
            $t->string('reference')->unique();
            $t->uuid('idempotency_key');
            $t->foreignId('user_id')->constrained();
            $t->foreignId('trip_id')->constrained();
            $t->foreignId('vendor_id')->constrained();
            $t->foreignId('promotion_id')->nullable()->constrained();
            $t->unsignedInteger('participants');
            $t->string('contact_name');
            $t->string('contact_phone', 30);
            $t->unsignedBigInteger('subtotal');
            $t->unsignedBigInteger('discount')->default(0);
            $t->unsignedBigInteger('total');
            $t->string('status')->default('awaiting_payment');
            $t->timestamp('expires_at');
            $t->timestamps();
            $t->unique(['user_id', 'idempotency_key']);
            $t->index(['user_id', 'status']);
            $t->index(['vendor_id', 'status']);
            $t->index(['status', 'expires_at']);
        });
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('booking_id')->unique()->constrained();
            $t->string('reference')->unique();
            $t->unsignedBigInteger('amount');
            $t->string('status')->default('pending');
            $t->string('provider')->default('midtrans');
            $t->string('provider_reference')->nullable()->unique();
            $t->string('checkout_url', 2048)->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
        });
        Schema::create('refunds', function (Blueprint $t) {
            $t->id();
            $t->foreignId('booking_id')->unique()->constrained();
            $t->foreignId('user_id')->constrained();
            $t->unsignedBigInteger('amount');
            $t->text('reason');
            $t->string('status')->default('pending');
            $t->foreignId('approved_by')->nullable()->constrained('users');
            $t->string('provider_reference')->nullable()->unique();
            $t->timestamps();
        });
        Schema::create('payouts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('booking_id')->unique()->constrained();
            $t->foreignId('vendor_id')->constrained();
            $t->unsignedBigInteger('amount');
            $t->string('status')->default('eligible');
            $t->foreignId('approved_by')->nullable()->constrained('users');
            $t->string('provider_reference')->nullable()->unique();
            $t->timestamps();
        });
        Schema::create('ledger_entries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('booking_id')->constrained();
            $t->string('reference')->unique();
            $t->string('account');
            $t->bigInteger('amount');
            $t->string('description');
            $t->timestamps();
            $t->index(['booking_id', 'account']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('trips');
        Schema::dropIfExists('vendors');
    }
};
