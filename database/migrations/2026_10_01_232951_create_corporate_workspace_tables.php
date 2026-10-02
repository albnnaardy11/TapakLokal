<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_companies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users');
            $table->string('name', 180);
            $table->string('pic_name', 150);
            $table->string('position', 100);
            $table->string('work_email', 254);
            $table->string('phone', 30);
            $table->string('budget_range', 50);
            $table->string('source', 100);
            $table->string('status', 30)->default('pending');
            $table->text('verification_note')->nullable();
            $table->unsignedBigInteger('monthly_limit')->nullable();
            $table->timestamp('consented_at');
            $table->boolean('marketing_consent')->default(false);
            $table->timestamps();
            $table->index(['owner_id', 'id']);
            $table->index(['status', 'id']);
        });
        Schema::create('corporate_memberships', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('corporate_company_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('role', 30);
            $table->string('status', 30)->default('invited');
            $table->timestamps();
            $table->unique(['corporate_company_id', 'user_id']);
            $table->index(['user_id', 'status', 'id']);
        });
        Schema::create('corporate_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('corporate_company_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('reference', 40)->unique();
            $table->uuid('idempotency_key');
            $table->string('title', 180);
            $table->string('destination', 150);
            $table->date('departure_date');
            $table->date('end_date');
            $table->unsignedInteger('participants');
            $table->unsignedBigInteger('budget');
            $table->string('cost_center', 100);
            $table->text('needs');
            $table->string('status', 30)->default('submitted');
            $table->foreignId('trip_id')->nullable()->constrained();
            $table->unsignedBigInteger('quote_total')->nullable();
            $table->json('quote')->nullable();
            $table->json('quote_history')->nullable();
            $table->timestamp('quote_expires_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->foreignId('booking_id')->nullable()->unique()->constrained();
            $table->timestamps();
            $table->unique(['corporate_company_id', 'user_id', 'idempotency_key'], 'corporate_request_idempotency');
            $table->index(['corporate_company_id', 'status', 'id']);
            $table->index(['corporate_company_id', 'departure_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_requests');
        Schema::dropIfExists('corporate_memberships');
        Schema::dropIfExists('corporate_companies');
    }
};
