<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Tests\Php54;

class Php54Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideClassUsesValid
     */
    public function testClassUsesValid($classOrObject)
    {
        $this->assertSame(array(), class_uses($classOrObject));
    }

    public function provideClassUsesValid()
    {
        return array(
            array('stdClass'),
            array(new \stdClass()),
            array('Iterator'),
        );
    }

    public function testClassUsesInvalid()
    {
        $this->assertFalse(@class_uses('NotDefined'));
    }

    public function testHexToBinValid()
    {
        $this->assertEquals("\x61\x62\x00\x63\x64", hex2bin("6162006364")); // With null byte
        $this->assertEquals("\x61\x62\x63\x64", hex2bin("61626364"));
    }

    public function testHexToBinInvalid()
    {
        $this->assertNull(@hex2bin(array())); // Invalid type 
        $this->assertFalse(@hex2bin("123")); // Invalid string length
    }
}
