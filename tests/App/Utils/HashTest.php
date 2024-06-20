<?php

declare(strict_types=1);

namespace App\Utils;

use PHPUnit\Framework\TestCase;
use function strlen;

class HashTest extends TestCase
{
    /**
     * @covers App\Utils\Hash::cookieHash
     */
    public function testCookieHash()
    {
        $_ENV['key'] = 'cookie_key';
        $passHash = 'password';
        $expire_in = 69420;
        $result = Hash::cookieHash($passHash, $expire_in);
        $this->assertIsString($result);
        $this->assertEquals(45, strlen($result));
        $this->assertEquals('e91053c4a7d6cc7fa5eb900b1ad96df484483ceace12a', $result);
    }

    /**
     * @covers App\Utils\Hash::ipHash
     */
    public function testIpHash()
    {
        $_ENV['key'] = 'cookie_key';
        $ip = '192.168.0.1';
        $uid = 69;
        $expire_in = 69420;
        $result = Hash::ipHash($ip, $uid, $expire_in);
        $this->assertIsString($result);
        $this->assertEquals(45, strlen($result));
        $this->assertEquals('522b51095b778f9f107153f75be554be1f8a8f2c1f4b4', $result);
    }

    /**
     * @covers App\Utils\Hash::deviceHash
     */
    public function testDeviceHash()
    {
        $_ENV['key'] = 'cookie_key';
        $device = 'Firefox/119.0';
        $uid = 69;
        $expire_in = 69420;
        $result = Hash::deviceHash($device, $uid, $expire_in);
        $this->assertIsString($result);
        $this->assertEquals(45, strlen($result));
        $this->assertEquals('1fd5a37cc8769c01a49f6eb9c167dc6ee6cc842913dba', $result);
    }

    /**
     * @covers App\Utils\Hash::checkPassword
     * @covers App\Utils\Hash::passwordHash
     * @covers App\Utils\Hash::sha256WithSalt
     * @covers App\Utils\Hash::sha3WithSalt
     */
    public function testPasswordFunctions()
    {
        $_ENV['salt'] = 'password_salt';
        $_ENV['pwdMethod'] = 'bcrypt';
        $password = 'password';
        $hashedPassword = Hash::passwordHash($password);
        $this->assertTrue(Hash::checkPassword($hashedPassword, $password));
        $this->assertFalse(Hash::checkPassword($hashedPassword, 'wrong_password'));
        $_ENV['pwdMethod'] = 'argon2i';
        $hashedPassword = Hash::passwordHash($password);
        $this->assertTrue(Hash::checkPassword($hashedPassword, $password));
        $this->assertFalse(Hash::checkPassword($hashedPassword, 'wrong_password'));
        $_ENV['pwdMethod'] = 'argon2id';
        $hashedPassword = Hash::passwordHash($password);
        $this->assertTrue(Hash::checkPassword($hashedPassword, $password));
        $this->assertFalse(Hash::checkPassword($hashedPassword, 'wrong_password'));
        $_ENV['pwdMethod'] = 'sha256';
        $hashedPassword = Hash::passwordHash($password);
        $this->assertTrue(Hash::checkPassword($hashedPassword, $password));
        $this->assertFalse(Hash::checkPassword($hashedPassword, 'wrong_password'));
        $_ENV['pwdMethod'] = 'sha3';
        $hashedPassword = Hash::passwordHash($password);
        $this->assertTrue(Hash::checkPassword($hashedPassword, $password));
        $this->assertFalse(Hash::checkPassword($hashedPassword, 'wrong_password'));
    }
}
