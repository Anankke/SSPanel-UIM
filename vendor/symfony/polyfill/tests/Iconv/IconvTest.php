<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Tests\Iconv;

use Symfony\Polyfill\Iconv\Iconv as p;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @covers Symfony\Polyfill\Iconv\Iconv::<!public>
 */
class IconvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv
     */
    public function testIconv()
    {
        // Native iconv() behavior varies between versions and OS for these two tests
        // See e.g. https://bugs.php.net/52211
        if (defined('HHVM_VERSION') ? HHVM_VERSION_ID >= 30901 : (PHP_VERSION_ID >= 50610)) {
            $this->assertFalse(@iconv('UTF-8', 'ISO-8859-1', 'nœud'));
            $this->assertSame('nud', iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
        }

        $this->assertSame(utf8_decode('déjà'), iconv('CP1252', 'ISO-8859-1', utf8_decode('déjà')));
        $this->assertSame('deja noeud', p::iconv('UTF-8', 'US-ASCII//TRANSLIT', 'déjà nœud'));

        $this->assertSame('4', iconv('UTF-8', 'UTF-8', 4));
    }

    /**
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_strlen
     * @covers Symfony\Polyfill\Iconv\Iconv::strlen1
     * @covers Symfony\Polyfill\Iconv\Iconv::strlen2
     */
    public function testIconvStrlen()
    {
        $this->assertSame(4, iconv_strlen('déjà', 'UTF-8'));
        $this->assertSame(3, iconv_strlen('한국어', 'UTF-8'));

        $this->assertSame(4, p::strlen2('déjà'));
        $this->assertSame(3, p::strlen2('한국어'));
    }

    /**
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_strpos
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_strrpos
     */
    public function testIconvStrPos()
    {
        $this->assertSame(1, iconv_strpos('11--', '1-', 0, 'UTF-8'));
        $this->assertSame(2, iconv_strpos('-11--', '1-', 0, 'UTF-8'));
        $this->assertSame(false, iconv_strrpos('한국어', '', 'UTF-8'));
        $this->assertSame(1, iconv_strrpos('한국어', '국', 'UTF-8'));
    }

    /**
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_substr
     */
    public function testIconvSubstr()
    {
        $this->assertSame('x', iconv_substr('x', 0, 1, 'UTF-8'));
    }

    /**
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_mime_encode
     */
    public function testIconvMimeEncode()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM incompatible.');
        }
        $text = "\xE3\x83\x86\xE3\x82\xB9\xE3\x83\x88\xE3\x83\x86\xE3\x82\xB9\xE3\x83\x88";
        $options = array(
            'scheme' => 'Q',
            'input-charset' => 'UTF-8',
            'output-charset' => 'UTF-8',
            'line-length' => 30,
        );

        $this->assertSame(
            "Subject: =?UTF-8?Q?=E3=83=86?=\r\n =?UTF-8?Q?=E3=82=B9?=\r\n =?UTF-8?Q?=E3=83=88?=\r\n =?UTF-8?Q?=E3=83=86?=\r\n =?UTF-8?Q?=E3=82=B9?=\r\n =?UTF-8?Q?=E3=83=88?=",
            iconv_mime_encode('Subject', $text, $options)
        );
    }

    /**
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_mime_decode
     */
    public function testIconvMimeDecode()
    {
        $this->assertSame('Legal encoded-word: * .', iconv_mime_decode('Legal encoded-word: =?utf-8?B?Kg==?= .'));
        $this->assertSame('Legal encoded-word: * .', iconv_mime_decode('Legal encoded-word: =?utf-8?Q?*?= .'));
        if (!defined('HHVM_VERSION') && '\\' !== DIRECTORY_SEPARATOR) {
            $this->assertSame('Illegal encoded-word:  .', iconv_mime_decode('Illegal encoded-word: =?utf-8?Q??= .', ICONV_MIME_DECODE_CONTINUE_ON_ERROR));
            $this->assertSame('Illegal encoded-word: .', iconv_mime_decode('Illegal encoded-word: =?utf-8?Q?'.chr(0xA1).'?= .', ICONV_MIME_DECODE_CONTINUE_ON_ERROR));
        }
    }

    /**
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_mime_decode
     */
    public function testIconvMimeDecodeIllegal()
    {
        iconv_mime_decode('Legal encoded-word: =?utf-8?Q?*?= .');
        $this->setExpectedException('PHPUnit_Framework_Error_Notice', 'Detected an illegal character in input string');
        iconv_mime_decode('Illegal encoded-word: =?utf-8?Q?'.chr(0xA1).'?= .');
    }

    /**
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_mime_decode_headers
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_mime_decode
     */
    public function testIconvMimeDecodeHeaders()
    {
        $headers = <<<HEADERS
From: =?UTF-8?B?PGZvb0BleGFtcGxlLmNvbT4=?=
Subject: =?ks_c_5601-1987?B?UkU6odk=?= Foo
X-Bar: =?cp949?B?UkU6odk=?= Foo
X-Bar: =?cp949?B?UkU6odk=?= =?UTF-8?Q?Bar?=
To: <test@example.com>
HEADERS;

        $result = array(
            'From' => '<foo@example.com>',
            'Subject' => '=?ks_c_5601-1987?B?UkU6odk=?= Foo',
            'X-Bar' => array(
                'RE:☆ Foo',
                'RE:☆Bar',
            ),
            'To' => '<test@example.com>',
        );

        $this->assertSame($result, iconv_mime_decode_headers($headers, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8'));
    }

    /**
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_get_encoding
     * @covers Symfony\Polyfill\Iconv\Iconv::iconv_set_encoding
     */
    public function testIconvGetEncoding()
    {
        $a = array(
           'input_encoding' => 'UTF-8',
           'output_encoding' => 'UTF-8',
           'internal_encoding' => 'UTF-8',
        );

        foreach ($a as $t => $e) {
            $this->assertTrue(@iconv_set_encoding($t, $e));
            $this->assertSame($e, iconv_get_encoding($t));
        }

        $this->assertSame($a, iconv_get_encoding('all'));

        $this->assertFalse(@iconv_set_encoding('foo', 'UTF-8'));
    }
}
