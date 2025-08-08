<?php

declare(strict_types=1);

use App\Services\DynamicRate;

it('gets full day rates', function () {
    $max_rate = 3;
    $max_rate_time = 22;
    $min_rate = 0.5;
    $min_rate_time = 3;
    $method = 'logistic';

    $expected_rates = [
        2.14, 1.36, 0.81, 0.5, 0.51, 0.51, 0.53, 0.55, 0.6, 0.7, 0.87, 1.15,
        1.53, 1.97, 2.35, 2.63, 2.8, 2.9, 2.95, 2.97, 2.99, 2.99, 3.0, 2.69,
    ];

    $rates = DynamicRate::getFullDayRates($max_rate, $max_rate_time, $min_rate, $min_rate_time, $method);

    expect($rates)->toBe($expected_rates);
});

it('gets rate by time', function () {
    $max_rate = 3;
    $max_rate_time = 22;
    $min_rate = 0.5;
    $min_rate_time = 3;
    $method = 'logistic';

    $expected_rate = 2.69;

    $rate = DynamicRate::getRateByTime($max_rate, $max_rate_time, $min_rate, $min_rate_time, -1, $method);

    expect($rate)->toBe($expected_rate);
});

it('validates data correctly', function () {
    $max_rate = 3;
    $max_rate_time = 22;
    $min_rate = 0.5;
    $min_rate_time = 3;

    expect(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time))->toBeTrue();

    // Test with invalid max rate
    $max_rate = -1;
    expect(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time))->toBeFalse();

    // Test with invalid max rate time
    $max_rate = 3;
    $max_rate_time = 25;
    expect(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time))->toBeFalse();

    // Test with invalid min rate
    $max_rate_time = 22;
    $min_rate = -1;
    expect(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time))->toBeFalse();

    // Test with invalid min rate time
    $min_rate = 0.5;
    $min_rate_time = -1;
    expect(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time))->toBeFalse();
});

it('calculates logistic rate', function () {
    $max_rate = 3;
    $max_rate_time = 22;
    $min_rate = 0.5;
    $min_rate_time = 3;
    $time = 6;

    $expected_rate = 0.53;

    $rate = DynamicRate::logistic($max_rate, $max_rate_time, $min_rate, $min_rate_time, $time);

    expect($rate)->toBe($expected_rate);
});

it('calculates linear rate', function () {
    $max_rate = 3;
    $max_rate_time = 22;
    $min_rate = 0.5;
    $min_rate_time = 3;
    $time = 6;

    $expected_rate = 0.89;

    $rate = DynamicRate::linear($max_rate, $max_rate_time, $min_rate, $min_rate_time, $time);

    expect($rate)->toBe($expected_rate);
});