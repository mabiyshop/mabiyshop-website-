<?php

namespace App\Support;

final class BangladeshMobile
{
    public static function normalize($value): string
    {
        return strtr(trim((string) $value), [
            '০' => '0',
            '১' => '1',
            '২' => '2',
            '৩' => '3',
            '৪' => '4',
            '৫' => '5',
            '৬' => '6',
            '৭' => '7',
            '৮' => '8',
            '৯' => '9',
        ]);
    }

    public static function validationRules(): array
    {
        return ['required', 'regex:/^01[0-9]{9}$/'];
    }

    private function __construct()
    {
    }
}
