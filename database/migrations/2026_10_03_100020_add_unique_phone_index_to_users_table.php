<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('users')->select('phone')->whereNotNull('phone')->groupBy('phone')->havingRaw('count(*) > 1')->exists();
        if ($duplicates) {
            throw new RuntimeException('Duplicate user phone numbers exist. Resolve account ownership before applying this migration. No accounts were changed.');
        }
        $seen = [];
        DB::table('users')->select('id', 'phone')->whereNotNull('phone')->chunkById(500, function ($users) use (&$seen): void {
            foreach ($users as $user) {
                if (! preg_match('/^[+0-9() .-]+$/D', $user->phone)) {
                    continue;
                }
                $number = str_replace([' ', '(', ')', '.', '-'], '', $user->phone);
                if (str_starts_with($number, '0')) {
                    $number = '+62'.substr($number, 1);
                } elseif (str_starts_with($number, '62')) {
                    $number = '+'.$number;
                }
                if (! preg_match('/^\+[1-9][0-9]{7,14}$/D', $number)) {
                    continue;
                }
                if (isset($seen[$number])) {
                    throw new RuntimeException('Equivalent user phone numbers exist. Resolve account ownership before applying this migration. No accounts were changed.');
                }
                $seen[$number] = true;
            }
        });
        Schema::table('users', function (Blueprint $table): void {
            $table->unique('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['phone']);
        });
    }
};
