<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class PhoneNumberService
{
    public static function normalize(string $phone): ?string
    {
        if (! preg_match('/^[+0-9() .-]+$/D', $phone)) {
            return null;
        }
        $number = str_replace([' ', '(', ')', '.', '-'], '', $phone);
        if (str_starts_with($number, '0')) {
            $number = '+62'.substr($number, 1);
        } elseif (str_starts_with($number, '62')) {
            $number = '+'.$number;
        }

        return preg_match('/^\+[1-9][0-9]{7,14}$/D', $number) ? $number : null;
    }

    /** @return Builder<User> */
    public static function users(string $phone): Builder
    {
        $variants = [$phone, ltrim($phone, '+')];
        if (str_starts_with($phone, '+62')) {
            $variants[] = '0'.substr($phone, 3);
        }
        $placeholders = implode(',', array_fill(0, count($variants), '?'));

        return User::query()->whereRaw("replace(replace(replace(replace(replace(phone, ' ', ''), '-', ''), '(', ''), ')', ''), '.', '') in ({$placeholders})", $variants);
    }

    public static function forAccount(string $phone, ?int $exceptUserId = null): string
    {
        $normalized = self::normalize($phone);
        if ($normalized === null || self::users($normalized)->when($exceptUserId !== null, fn (Builder $query) => $query->whereKeyNot($exceptUserId))->exists()) {
            throw ValidationException::withMessages(['phone' => 'Nomor HP tidak valid atau tidak tersedia. Gunakan format 08… atau +kode negara.']);
        }

        return $normalized;
    }
}
