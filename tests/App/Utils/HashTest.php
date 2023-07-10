<?php

declare(strict_types=1);

namespace App\Utils;

use PHPUnit\Framework\TestCase;
use App\Utils\Hash;

class HashTest extends TestCase
{
    /**
     * @covers App\Utils\Hash::cookieHash
     */
    public function testCookieHash()
    {
        $_ENV['key'] = 'cookie_key';
        $passHash = 'password';
        $expire_in = '1 hour';
        $result = Hash::cookieHash($passHash, $expire_in);
        $this->assertIsString($result);
        $this->assertEquals(45, strlen($result));
    }

    /**
     * @covers App\Utils\Hash::ipHash
     */
    public function testIpHash()
    {
        $_ENV['key'] = 'cookie_key';
        $ip = '192.168.0.1';
        $uid = 'user_id';
        $expire_in = '1 hour';
        $result = Hash::ipHash($ip, $uid, $expire_in);
        $this->assertIsString($result);
        $this->assertEquals(45, strlen($result));
    }

    /**
     * @covers App\Utils\Hash::checkPassword
     * @covers App\Utils\Hash::passwordHash
     * @covers App\Utils\Hash::md5WithSalt
     * @covers App\Utils\Hash::sha256WithSalt
     */
    public function testPasswordFunctions()
    {
        $_ENV['salt'] = 'password_salt';
        $_ENV['pwdMethod'] = 'bcrypt';
        $password = 'password';
        $hashedPassword = Hash::passwordHash($password);
        $this->assertTrue(Hash::checkPassword($hashedPassword, $password));
        $this->assertFalse(Hash::checkPassword($hashedPassword, 'wrong_password'));
        $this->assertIsString(Hash::passwordHash($password));
        $this->assertIsString(Hash::md5WithSalt($password));
        $this->assertIsString(Hash::sha256WithSalt($password));
    }
}

