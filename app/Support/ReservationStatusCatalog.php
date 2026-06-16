<?php

namespace App\Support;

class ReservationStatusCatalog
{
    public const PENDING = 'pending';
    public const ACCEPTED = 'accepted';
    public const REFUSED = 'refused';
    public const CANCELLED = 'cancelled';

    public static function all(): array
    {
        return [
            self::PENDING,
            self::ACCEPTED,
            self::REFUSED,
            self::CANCELLED,
        ];
    }

    public static function active(): array
    {
        return [
            self::PENDING,
            self::ACCEPTED,
        ];
    }

    public static function pendingStates(): array
    {
        return [
            self::PENDING,
        ];
    }

    public static function refusedOrCancelled(): array
    {
        return [
            self::REFUSED,
            self::CANCELLED,
        ];
    }
}
