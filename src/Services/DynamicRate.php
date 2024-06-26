<?php

declare(strict_types=1);

namespace App\Services;

final class DynamicRate
{
    public static function getFullDayRates(
        float $max_rate,
        int $max_rate_time,
        float $min_rate,
        int $min_rate_time,
        string $method = 'logistic',
    ): array {
        if (! self::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time)) {
            return [];
        }

        $rates = [];

        for ($i = 0; $i < 24; $i++) {
            $rates[] = self::getRateByTime($max_rate, $max_rate_time, $min_rate, $min_rate_time, $i, $method);
        }

        return $rates;
    }

    public static function getRateByTime(
        float $max_rate,
        int $max_rate_time,
        float $min_rate,
        int $min_rate_time,
        int $time,
        string $method = 'logistic',
    ): float {
        if (! self::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time)) {
            return 1;
        }

        if ($time === $max_rate_time || $max_rate_time === $min_rate_time || $max_rate === $min_rate) {
            return $max_rate;
        }

        if ($time === $min_rate_time) {
            return $min_rate;
        }

        if ($time < $min_rate_time) {
            $time += 24;
        }

        if ($time > $max_rate_time) {
            $min_rate_time += 24;
        }

        return match ($method) {
            'logistic' => self::logistic($max_rate, $max_rate_time, $min_rate, $min_rate_time, $time),
            'linear' => self::linear($max_rate, $max_rate_time, $min_rate, $min_rate_time, $time),
        };
    }

    public static function validateData(
        float $max_rate,
        int $max_rate_time,
        float $min_rate,
        int $min_rate_time,
    ): bool {
        return ! ($max_rate < 0 ||
            $min_rate < 0 ||
            $max_rate > 999 ||
            $min_rate > 999 ||
            $max_rate_time < 0 ||
            $min_rate_time < 0 ||
            $max_rate_time > 24 ||
            $min_rate_time > 24 ||
            $min_rate_time > $max_rate_time);
    }

    public static function logistic(
        float $max_rate,
        int $max_rate_time,
        float $min_rate,
        int $min_rate_time,
        int $time,
    ): float {
        $k = $time < $max_rate_time ? -0.7 : 1.3;
        $e = M_E;

        return round(
            ($max_rate - $min_rate) / (1 + $e ** ($k * ($time - ($max_rate_time + $min_rate_time) / 2))) + $min_rate,
            2
        );
    }

    public static function linear(
        float $max_rate,
        int $max_rate_time,
        float $min_rate,
        int $min_rate_time,
        int $time,
    ): float {
        $k = ($max_rate - $min_rate) / ($max_rate_time - $min_rate_time);
        $b = $max_rate - $k * $max_rate_time;

        return round(
            $k * $time + $b,
            2
        );
    }
}
