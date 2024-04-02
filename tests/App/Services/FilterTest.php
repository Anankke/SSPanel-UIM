<?php

declare(strict_types=1);

namespace App\Services;

use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    /**
     * @covers App\Services\Filter::checkEmailFilter
     */
    public function testCheckEmailFilter(): void
    {
        $_ENV['mail_filter'] = 1;
        $_ENV['mail_filter_list'] = ['example.com'];
        $this->assertTrue(Filter::checkEmailFilter('test@example.com'));
        $_ENV['mail_filter'] = 2;
        $_ENV['mail_filter_list'] = ['example.com'];
        $this->assertFalse(Filter::checkEmailFilter('test@example.com'));
        $this->assertFalse(Filter::checkEmailFilter('invalid_email'));
        $_ENV['mail_filter'] = 0;
        $this->assertTrue(Filter::checkEmailFilter('test@example.com'));
        $_ENV['mail_filter'] = 1;
        $_ENV['mail_filter_list'] = ['example.com'];
        $this->assertFalse(Filter::checkEmailFilter('test@notexample.com'));
        $_ENV['mail_filter'] = 2;
        $_ENV['mail_filter_list'] = ['example.com'];
        $this->assertTrue(Filter::checkEmailFilter('test@notexample.com'));
    }
}
