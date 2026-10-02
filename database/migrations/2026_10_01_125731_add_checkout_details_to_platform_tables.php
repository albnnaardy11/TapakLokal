<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('payment_preferences')->nullable();
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('contact_email')->nullable();
            $table->json('traveler_details')->nullable();
            $table->text('special_request')->nullable();
        });
        Schema::table('souvenir_orders', function (Blueprint $table) {
            $table->string('contact_email')->nullable();
        });
        foreach (['payments', 'souvenir_payments'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->string('method', 30)->nullable();
                $table->json('instructions')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['payments', 'souvenir_payments'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropColumn(['method', 'instructions']));
        }
        Schema::table('souvenir_orders', fn (Blueprint $table) => $table->dropColumn('contact_email'));
        Schema::table('bookings', fn (Blueprint $table) => $table->dropColumn(['contact_email', 'traveler_details', 'special_request']));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('payment_preferences'));
    }
};
