<?php

namespace App\Support;

class DentalCaseCatalog
{
    public const HEALTHY = 'Healthy';
    public const CAVITY = 'Cavity';
    public const INFECTION = 'Infection';

    public const FRONT_RESULTS = [
        self::HEALTHY,
        self::CAVITY,
        self::INFECTION,
    ];

    public const ALL_RESULTS = [
        self::HEALTHY,
        self::CAVITY,
        self::INFECTION,
        'Calculus',
        'Caries',
        'Gingivitis',
        'Hypodontia',
        'Tooth Discoloration',
        'Ulcers',
    ];

    public static function frontResults(): array
    {
        return self::FRONT_RESULTS;
    }

    public static function allResults(): array
    {
        return self::ALL_RESULTS;
    }

    public static function normalize(?string $result): ?string
    {
        if (!$result) {
            return null;
        }

        if (in_array($result, self::FRONT_RESULTS, true)) {
            return $result;
        }

        if (in_array($result, ['Calculus', 'Caries'], true)) {
            return self::CAVITY;
        }

        if (in_array($result, ['Gingivitis', 'Hypodontia', 'Tooth Discoloration', 'Ulcers'], true)) {
            return self::INFECTION;
        }

        return $result;
    }

    public static function isCritical(?string $result): bool
    {
        $normalized = self::normalize($result);

        return $normalized !== null && $normalized !== self::HEALTHY;
    }
}
