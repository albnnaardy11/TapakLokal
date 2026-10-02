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
        Schema::create('souvenir_products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('vendor_id')->constrained();
            $t->string('slug')->unique();
            $t->string('name');
            $t->string('category');
            $t->string('region');
            $t->text('description');
            $t->text('care')->nullable();
            $t->string('image_url', 2048)->nullable();
            $t->json('variants');
            $t->unsignedBigInteger('price');
            $t->unsignedInteger('stock');
            $t->unsignedInteger('reserved_stock')->default(0);
            $t->unsignedInteger('weight');
            $t->unsignedSmallInteger('preparation_days')->default(1);
            $t->string('availability')->default('Ready stock');
            $t->boolean('pickup_only')->default(true);
            $t->text('pickup_address');
            $t->json('delivery_rates')->nullable();
            $t->string('status')->default('draft');
            $t->timestamps();
            $t->index(['status', 'id']);
            $t->index(['vendor_id', 'status']);
            $t->index(['status', 'region', 'category']);
        });
        Schema::create('souvenir_cart_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained();
            $t->foreignId('souvenir_product_id')->constrained();
            $t->string('variant', 100);
            $t->unsignedInteger('quantity');
            $t->string('note', 500)->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'souvenir_product_id', 'variant'], 'souvenir_cart_unique');
        });
        Schema::create('souvenir_orders', function (Blueprint $t) {
            $t->id();
            $t->string('reference')->unique();
            $t->foreignId('user_id')->constrained();
            $t->foreignId('vendor_id')->constrained();
            $t->uuid('idempotency_key');
            $t->string('request_hash', 64);
            $t->string('contact_name');
            $t->string('contact_phone', 30);
            $t->string('method');
            $t->text('address');
            $t->date('pickup_date')->nullable();
            $t->string('delivery_service')->nullable();
            $t->unsignedBigInteger('shipping_fee')->default(0);
            $t->unsignedBigInteger('subtotal');
            $t->unsignedBigInteger('total');
            $t->unsignedBigInteger('vendor_amount');
            $t->unsignedBigInteger('platform_fee');
            $t->string('status')->default('awaiting_payment');
            $t->timestamp('expires_at');
            $t->string('tracking_number')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'idempotency_key']);
            $t->index(['status', 'expires_at']);
            $t->index(['user_id', 'id']);
            $t->index(['vendor_id', 'status', 'id']);
        });
        Schema::create('souvenir_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('souvenir_order_id')->constrained();
            $t->foreignId('souvenir_product_id')->constrained();
            $t->string('name');
            $t->string('variant', 100);
            $t->unsignedInteger('quantity');
            $t->unsignedBigInteger('unit_price');
            $t->unsignedBigInteger('vendor_price');
            $t->string('note', 500)->nullable();
            $t->timestamps();
        });
        Schema::create('souvenir_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('souvenir_order_id')->unique()->constrained();
            $t->string('reference')->unique();
            $t->unsignedBigInteger('amount');
            $t->string('status')->default('pending');
            $t->string('checkout_url', 2048)->nullable();
            $t->string('provider_reference')->nullable()->unique();
            $t->timestamp('paid_at')->nullable();
            $t->timestamp('reconciled_at')->nullable();
            $t->timestamps();
            $t->index(['status', 'reconciled_at']);
        });
        Schema::create('souvenir_ledger_entries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('souvenir_order_id')->constrained();
            $t->string('reference')->unique();
            $t->string('account');
            $t->bigInteger('amount');
            $t->timestamps();
        });
        Schema::create('souvenir_saved_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained();
            $t->string('kind');
            $t->unsignedBigInteger('target_id');
            $t->unique(['user_id', 'kind', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['souvenir_saved_items', 'souvenir_ledger_entries', 'souvenir_payments', 'souvenir_order_items', 'souvenir_orders', 'souvenir_cart_items', 'souvenir_products'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
