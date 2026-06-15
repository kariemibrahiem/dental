<?php

namespace App\Support;

use Illuminate\Support\Str;

class IdentityGenerator
{
    public static function uniqueEmail(string $modelClass, string $name, string $prefix): string
    {
        $slug = Str::slug($name ?: $prefix, '.');
        $slug = $slug !== '' ? $slug : $prefix;
        $stamp = now()->format('YmdHis');
        $counter = 1;

        do {
            $suffix = $counter === 1 ? $stamp : "{$stamp}{$counter}";
            $email = "{$prefix}.{$slug}.{$suffix}@ai-dental.local";
            $counter++;
        } while ($modelClass::where('email', $email)->exists());

        return $email;
    }

    public static function temporaryPassword(int $length = 10): string
    {
        return Str::random($length);
    }

    public static function uniqueCode(string $modelClass, string $column = 'code', int $length = 10): string
    {
        do {
            $code = Str::upper(Str::random($length));
        } while ($modelClass::where($column, $code)->exists());

        return $code;
    }
}
