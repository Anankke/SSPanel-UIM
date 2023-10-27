<?php

declare(strict_types=1);

namespace App\Services;

use PHPUnit\Framework\TestCase;

class DynamicRateTest extends TestCase
{
    /**
     * @covers App\Services\DynamicRate::getFullDayRates
     */
    public function testGetFullDayRates(): void
    {
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

        $this->assertSame($expected_rates, $rates);
    }

    /**
     * @covers App\Services\DynamicRate::getRateByTime
     */
    public function testGetRateByTime(): void
    {
        $max_rate = 3;
        $max_rate_time = 22;
        $min_rate = 0.5;
        $min_rate_time = 3;
        $method = 'logistic';

        $expected_rate = 2.69;

        $rate = DynamicRate::getRateByTime($max_rate, $max_rate_time, $min_rate, $min_rate_time, -1, $method);

        $this->assertSame($expected_rate, $rate);
    }

    /**
     * @covers App\Services\DynamicRate::validateData
     */
    public function testValidateData(): void
    {
        $max_rate = 3;
        $max_rate_time = 22;
        $min_rate = 0.5;
        $min_rate_time = 3;

        $this->assertTrue(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time));

        $max_rate = -1;

        $this->assertFalse(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time));

        $max_rate = 3;
        $max_rate_time = 25;

        $this->assertFalse(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time));

        $max_rate_time = 22;
        $min_rate = -1;

        $this->assertFalse(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time));

        $min_rate = 0.5;
        $min_rate_time = -1;

        $this->assertFalse(DynamicRate::validateData($max_rate, $max_rate_time, $min_rate, $min_rate_time));
    }

    /**
     * @covers App\Services\DynamicRate::logistic
     */
    public function testLogistic(): void
    {
        $max_rate = 3;
        $max_rate_time = 22;
        $min_rate = 0.5;
        $min_rate_time = 3;
        $time = 6;

        $expected_rate = 0.53;

        $rate = DynamicRate::logistic($max_rate, $max_rate_time, $min_rate, $min_rate_time, $time);

        $this->assertSame($expected_rate, $rate);
    }

    /**
     * @covers App\Services\DynamicRate::linear
     */
    public function testLinear(): void
    {
        $max_rate = 3;
        $max_rate_time = 22;
        $min_rate = 0.5;
        $min_rate_time = 3;
        $time = 6;

        $expected_rate = 0.89;

        $rate = DynamicRate::linear($max_rate, $max_rate_time, $min_rate, $min_rate_time, $time);

        $this->assertSame($expected_rate, $rate);
    }
}
