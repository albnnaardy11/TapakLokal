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
        Schema::create('traveler_profiles', function (Blueprint $t) { $t->id(); $t->foreignId('user_id')->constrained(); $t->string('name'); $t->date('birth_date')->nullable(); $t->string('phone', 30)->nullable(); $t->string('emergency_contact')->nullable(); $t->timestamps(); });
        Schema::create('favorites', function (Blueprint $t) { $t->id(); $t->foreignId('user_id')->constrained(); $t->foreignId('trip_id')->constrained()->cascadeOnDelete(); $t->timestamps(); $t->unique(['user_id', 'trip_id']); });
        Schema::create('reviews', function (Blueprint $t) { $t->id(); $t->foreignId('user_id')->constrained(); $t->foreignId('booking_id')->unique()->constrained(); $t->foreignId('trip_id')->constrained(); $t->unsignedTinyInteger('rating'); $t->text('body'); $t->text('vendor_response')->nullable(); $t->string('status')->default('published'); $t->timestamps(); });
        Schema::create('reward_entries', function (Blueprint $t) { $t->id(); $t->foreignId('user_id')->constrained(); $t->foreignId('booking_id')->nullable()->constrained(); $t->string('reference')->unique(); $t->integer('points'); $t->string('description'); $t->timestamps(); });
        Schema::create('support_tickets', function (Blueprint $t) { $t->id(); $t->foreignId('user_id')->constrained(); $t->foreignId('booking_id')->nullable()->constrained(); $t->foreignId('vendor_id')->nullable()->constrained(); $t->string('subject'); $t->string('category'); $t->string('status')->default('open'); $t->string('priority')->default('normal'); $t->timestamps(); $t->index(['user_id', 'status']); });
        Schema::create('support_messages', function (Blueprint $t) { $t->id(); $t->foreignId('support_ticket_id')->constrained()->cascadeOnDelete(); $t->foreignId('user_id')->constrained(); $t->text('body'); $t->timestamps(); });
        Schema::create('affiliates', function (Blueprint $t) { $t->id(); $t->foreignId('user_id')->unique()->constrained(); $t->string('code')->unique(); $t->unsignedInteger('commission_bps')->default(0); $t->string('status')->default('pending'); $t->timestamps(); });
        Schema::create('campaigns', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('type'); $t->text('description'); $t->date('starts_at'); $t->date('ends_at'); $t->string('status')->default('draft'); $t->timestamps(); });
        Schema::create('notifications', function (Blueprint $t) { $t->uuid('id')->primary(); $t->string('type'); $t->morphs('notifiable'); $t->text('data'); $t->timestamp('read_at')->nullable(); $t->timestamps(); });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('reward_entries');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('traveler_profiles');
    }
};
