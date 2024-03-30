<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Filter;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    /**
     * @covers App\Services\Filter::checkEmailFilter
     */
    public function testCheckEmailFilterWithValidEmailAndWhitelist(): void
    {
        $_ENV['mail_filter'] = 1;
        $_ENV['mail_filter_list'] = ['example.com'];
        $this->assertTrue(Filter::checkEmailFilter('test@example.com'));
    }

    /**
     * @covers App\Services\Filter::checkEmailFilter
     */
    public function testCheckEmailFilterWithValidEmailAndBlacklist(): void
    {
        $_ENV['mail_filter'] = 2;
        $_ENV['mail_filter_list'] = ['example.com'];
        $this->assertFalse(Filter::checkEmailFilter('test@example.com'));
    }

    /**
     * @covers App\Services\Filter::checkEmailFilter
     */
    public function testCheckEmailFilterWithInvalidEmail(): void
    {
        $this->assertFalse(Filter::checkEmailFilter('invalid_email'));
    }

    /**
     * @covers App\Services\Filter::checkEmailFilter
     */
    public function testCheckEmailFilterWithNoMailFilter(): void
    {
        $_ENV['mail_filter'] = 0;
        $this->assertTrue(Filter::checkEmailFilter('test@example.com'));
    }

    /**
     * @covers App\Services\Filter::checkEmailFilter
     */
    public function testCheckEmailFilterWithValidEmailNotInWhitelist(): void
    {
        $_ENV['mail_filter'] = 1;
        $_ENV['mail_filter_list'] = ['example.com'];
        $this->assertFalse(Filter::checkEmailFilter('test@notexample.com'));
    }

    /**
     * @covers App\Services\Filter::checkEmailFilter
     */
    public function testCheckEmailFilterWithValidEmailNotInBlacklist(): void
    {
        $_ENV['mail_filter'] = 2;
        $_ENV['mail_filter_list'] = ['example.com'];
        $this->assertTrue(Filter::checkEmailFilter('test@notexample.com'));
    }
}
