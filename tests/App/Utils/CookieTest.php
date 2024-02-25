<?php

declare(strict_types=1);

namespace App\Utils;

use PHPUnit\Framework\TestCase;
use App\Utils\Cookie;

final class CookieTest extends TestCase
{
    /**
     * @covers App\Utils\Cookie::set
     */
    public function testSet(): void
    {
        $data = ['testKey' => 'testValue'];
        $time = time() + 3600;

        Cookie::set($data, $time);

        $this->assertEquals('testValue', $_COOKIE['testKey']);
    }

    /**
     * @covers App\Utils\Cookie::setWithDomain
     */
    public function testSetWithDomain(): void
    {
        $data = ['testKey' => 'testValue'];
        $time = time() + 3600;
        $domain = 'localhost';

        Cookie::setWithDomain($data, $time, $domain);

        $this->assertEquals('testValue', $_COOKIE['testKey']);
    }

    /**
     * @covers App\Utils\Cookie::get
     */
    public function testGet(): void
    {
        $data = ['testKey' => 'testValue'];
        $time = time() + 3600;

        Cookie::set($data, $time);

        $this->assertEquals('testValue', Cookie::get('testKey'));
    }
}
