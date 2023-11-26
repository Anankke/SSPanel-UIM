<?php

declare(strict_types=1);

namespace App\Utils;

use MaxMind\Db\Reader\InvalidDatabaseException;
use PHPUnit\Framework\TestCase;
use function date_default_timezone_set;

class ToolsTest extends TestCase
{
    /**
     * @covers App\Utils\Tools::getIpLocation
     * @throws InvalidDatabaseException
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
     * @covers App\Utils\Tools::genRandomChar
     */
    public function testGenRandomChar()
    {
        $length = 10;
        $randomString = Tools::genRandomChar($length);
        $this->assertIsString($randomString);
        $this->assertEquals($length, strlen($randomString));
    }

    /**
     * @covers App\Utils\Tools::genSs2022UserPk
     */
    public function testGenSs2022UserPk()
    {
        $passwd = 'password';
        $length = 16;
        $pk = Tools::genSs2022UserPk($passwd, $length);
        $this->assertIsString($pk);
        $this->assertEquals('NWU4ODQ4OThkYTI4MDQ3MQ==', $pk);
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
     * @covers App\Utils\Tools::isParamValidate
     */
    public function testIsParamValidate()
    {
        $this->assertTrue(Tools::isParamValidate('default', 'aes-128-gcm'));
        $this->assertFalse(Tools::isParamValidate('default', 'rc4-md5'));
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
     * @covers App\Utils\Tools::isEmailLegal
     */
    public function testIsEmailLegal()
    {
        $_ENV['mail_filter'] = 1;
        $_ENV['mail_filter_list'] = ['example.com'];

        $email1 = 'test@example.com';
        $email2 = 'test@example.org';

        $expected1 = ['ret' => 1];
        $expected2 = ['ret' => 0, 'msg' => '邮箱域名 example.org 无效，请更换邮件地址'];

        $this->assertEquals($expected1, Tools::isEmailLegal($email1));
        $this->assertEquals($expected2, Tools::isEmailLegal($email2));
    }

    /**
     * @covers App\Utils\Tools::isIPv4
     */
    public function testIsIPv4()
    {
        $this->assertTrue(Tools::isIPv4('192.168.0.1'));
        $this->assertFalse(Tools::isIPv4('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
    }

    /**
     * @covers App\Utils\Tools::isIPv6
     */
    public function testIsIPv6()
    {
        $this->assertTrue(Tools::isIPv6('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
        $this->assertFalse(Tools::isIPv6('192.168.0.1'));
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
    }
}
