<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('reconciled_at')->default('1970-01-02 00:00:00');
            $table->index(['status', 'reconciled_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status', 'reconciled_at', 'id']);
            $table->dropColumn('reconciled_at');
        });
    }
};
