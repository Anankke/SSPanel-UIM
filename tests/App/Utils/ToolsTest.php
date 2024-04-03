<?php

declare(strict_types=1);

namespace App\Utils;

use MaxMind\Db\Reader\InvalidDatabaseException;
use PHPUnit\Framework\TestCase;
use function date_default_timezone_set;
use function strlen;

class ToolsTest extends TestCase
{
    /**
     * @covers App\Utils\Tools::getIpLocation
     */
    public function testGetIpLocation()
    {
        $_ENV['maxmind_license_key'] = '';
        $msg = Tools::getIpLocation('8.8.8.8');
        $this->assertIsString($msg);
        $this->assertEquals('GeoIP2 服务未配置', $msg);
    }

    /**
     * @covers App\Utils\Tools::autoBytes
     */
    public function testAutoBytes()
    {
        $size = 1024;
        $bytes = Tools::autoBytes($size);
        $this->assertIsString($bytes);
        $this->assertEquals('1KB', $bytes);
    }

    /**
     * @covers App\Utils\Tools::autoBytesR
     */
    public function testAutoBytesR()
    {
        $size = '1KB';
        $bytes = Tools::autoBytesR($size);
        $this->assertIsInt($bytes);
        $this->assertEquals(1024, $bytes);
    }

    /**
     * @covers App\Utils\Tools::autoMbps
     */
    public function testAutoMbps()
    {
        $bandwidth = 1;
        $mbps = Tools::autoMbps($bandwidth);
        $this->assertIsString($mbps);
        $this->assertEquals('1Mbps', $mbps);
    }

    /**
     * @covers App\Utils\Tools::toMB
     */
    public function testToMB()
    {
        $traffic = 1;
        $mb = 1048576;
        $result = Tools::toMB($traffic);
        $this->assertIsInt($result);
        $this->assertEquals($traffic * $mb, $result);
    }

    /**
     * @covers App\Utils\Tools::toGB
     */
    public function testToGB()
    {
        $traffic = 1;
        $gb = 1048576 * 1024;
        $result = Tools::toGB($traffic);
        $this->assertIsInt($result);
        $this->assertEquals($traffic * $gb, $result);
    }

    /**
     * @covers App\Utils\Tools::flowToGB
     */
    public function testFlowToGB()
    {
        $traffic = 1048576 * 1024;
        $gb = 1048576 * 1024;
        $result = Tools::flowToGB($traffic);
        $this->assertIsFloat($result);
        $this->assertEquals($traffic / $gb, $result);
    }

    /**
     * @covers App\Utils\Tools::flowToMB
     */
    public function testFlowToMB()
    {
        $traffic = 1048576;
        $mb = 1048576;
        $result = Tools::flowToMB($traffic);
        $this->assertIsFloat($result);
        $this->assertEquals($traffic / $mb, $result);
    }

    /**
     * @covers App\Utils\Tools::genSubToken
     */
    public function testGenSubToken()
    {
        $_ENV['sub_token_len'] = 10;
        $token = Tools::genSubToken();
        $this->assertEquals(10, strlen($token));
        $_ENV['sub_token_len'] = 0;
        $token = Tools::genSubToken();
        $this->assertEquals(8, strlen($token));
        $_ENV['sub_token_len'] = -5;
        $token = Tools::genSubToken();
        $this->assertEquals(8, strlen($token));
    }

    /**
     * @covers App\Utils\Tools::genRandomChar
     */
    public function testGenRandomChar()
    {
        $randomString = Tools::genRandomChar();
        $this->assertIsString($randomString);
        $this->assertEquals(8, strlen($randomString));
        $length = 10;
        $randomString = Tools::genRandomChar($length);
        $this->assertIsString($randomString);
        $this->assertEquals($length, strlen($randomString));
        $length = 9;
        $randomString = Tools::genRandomChar($length);
        $this->assertIsString($randomString);
        $this->assertEquals($length, strlen($randomString));
        $length = 1;
        $randomString = Tools::genRandomChar($length);
        $this->assertIsString($randomString);
        $this->assertEquals(2, strlen($randomString));
    }

    /**
     * @covers App\Utils\Tools::genSs2022UserPk
     */
    public function testGenSs2022UserPk()
    {
        $passwd = 'password';
        $method = '2022-blake3-aes-128-gcm';
        $pk = Tools::genSs2022UserPk($passwd, $method);
        $this->assertIsString($pk);
        $this->assertEquals('YzAwNjdkNGFmNGU4N2YwMA==', $pk);
        $method = '2022-blake3-aes-256-gcm';
        $pk = Tools::genSs2022UserPk($passwd, $method);
        $this->assertIsString($pk);
        $this->assertEquals('YzAwNjdkNGFmNGU4N2YwMGRiYWM2M2I2MTU2ODI4MjM=', $pk);
        $method = 'bomb_three_gorges_dam';
        $pk = Tools::genSs2022UserPk($passwd, $method);
        $this->assertFalse($pk);
    }

    /**
     * @covers App\Utils\Tools::toDateTime
     */
    public function testToDateTime()
    {
        date_default_timezone_set('ROC'); // Use Asia/Shanghai or PRC will cause this test to fail
        $time = 612907200; // 1989-06-04 04:00:00 UTC+8
        $expected = '1989-06-04 04:00:00';
        $result = Tools::toDateTime($time);
        $this->assertIsString($result);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers App\Utils\Tools::getDir
     */
    public function testGetDir()
    {
        // Scenario 1: Valid directory
        $dir1 = 'tests/testDir';
        $expected1 = ['emptyDir', 'file1', 'file2', 'file3']; // Replace with actual expected result
        $result1 = Tools::getDir($dir1);
        $this->assertEqualsCanonicalizing($expected1, $result1);

        // Scenario 2: Directory with .gitkeep
        $dir2 = 'tests/testDir/emptyDir';
        $result2 = Tools::getDir($dir2);
        $this->assertEqualsCanonicalizing(['.gitkeep'], $result2);
    }

    /**
     * @covers App\Utils\Tools::isParamValidate
     */
    public function testIsParamValidate()
    {
        $this->assertTrue(Tools::isParamValidate('default', 'aes-128-gcm'));
        $this->assertFalse(Tools::isParamValidate('default', 'rc4-md5'));
    }

    /**
     * @covers App\Utils\Tools::getSsMethod
     */
    public function testGetSsMethod()
    {
        // Scenario 1: ss_obfs
        $expected1 = [
            'simple_obfs_http',
            'simple_obfs_http_compatible',
            'simple_obfs_tls',
            'simple_obfs_tls_compatible',
        ];
        $result1 = Tools::getSsMethod('ss_obfs');
        $this->assertEquals($expected1, $result1);

        // Scenario 2: default
        $expected2 = [
            'aes-128-gcm',
            'aes-192-gcm',
            'aes-256-gcm',
            'chacha20-ietf-poly1305',
            'xchacha20-ietf-poly1305',
        ];
        $result2 = Tools::getSsMethod('default');
        $this->assertEquals($expected2, $result2);

        // Scenario 3: Random string
        $expected3 = [
            'aes-128-gcm',
            'aes-192-gcm',
            'aes-256-gcm',
            'chacha20-ietf-poly1305',
            'xchacha20-ietf-poly1305',
        ];
        $result3 = Tools::getSsMethod('randomString');
        $this->assertEquals($expected3, $result3);

        // Scenario 4: Empty string
        $expected4 = [
            'aes-128-gcm',
            'aes-192-gcm',
            'aes-256-gcm',
            'chacha20-ietf-poly1305',
            'xchacha20-ietf-poly1305',
        ];
        $result4 = Tools::getSsMethod('');
        $this->assertEquals($expected4, $result4);
    }

    /**
     * @covers App\Utils\Tools::isEmail
     */
    public function testIsEmail()
    {
        $this->assertTrue(Tools::isEmail('test@example.com'));
        $this->assertFalse(Tools::isEmail('test@example'));
    }

    /**
     * @covers App\Utils\Tools::isIPv4
     */
    public function testIsIPv4()
    {
        $this->assertTrue(Tools::isIPv4('192.168.0.1'));
        $this->assertFalse(Tools::isIPv4('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
        $this->assertFalse(Tools::isIPv4('UwU'));
    }

    /**
     * @covers App\Utils\Tools::isIPv6
     */
    public function testIsIPv6()
    {
        $this->assertTrue(Tools::isIPv6('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
        $this->assertFalse(Tools::isIPv6('192.168.0.1'));
        $this->assertFalse(Tools::isIPv6('hmm'));
    }

    /**
     * @covers App\Utils\Tools::isInt
     */
    public function testIsInt()
    {
        $this->assertTrue(Tools::isInt(123));
        $this->assertFalse(Tools::isInt('abc'));
    }

    /**
     * @covers App\Utils\Tools::isJson
     */
    public function testIsJson()
    {
        $this->assertTrue(Tools::isJson('{}'));
        $this->assertFalse(Tools::isJson('[]'));
        $this->assertFalse(Tools::isJson('what the'));
    }
}
