<?php
namespace JmesPath\Tests;

use JmesPath\Lexer;
use JmesPath\SyntaxErrorException;

/**
 * @covers JmesPath\Lexer
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    public function inputProvider()
    {
        return array(
            array('0', 'number'),
            array('1', 'number'),
            array('2', 'number'),
            array('3', 'number'),
            array('4', 'number'),
            array('5', 'number'),
            array('6', 'number'),
            array('7', 'number'),
            array('8', 'number'),
            array('9', 'number'),
            array('-1', 'number'),
            array('-1.5', 'number'),
            array('109.5', 'number'),
            array('.', 'dot'),
            array('{', 'lbrace'),
            array('}', 'rbrace'),
            array('[', 'lbracket'),
            array(']', 'rbracket'),
            array(':', 'colon'),
            array(',', 'comma'),
            array('||', 'or'),
            array('*', 'star'),
            array('foo', 'identifier'),
            array('"foo"', 'quoted_identifier'),
            array('`true`', 'literal'),
            array('`false`', 'literal'),
            array('`null`', 'literal'),
            array('`"true"`', 'literal')
        );
    }

    /**
     * @dataProvider inputProvider
     */
    public function testTokenizesInput($input, $type)
    {
        $l = new Lexer();
        $tokens = $l->tokenize($input);
        $this->assertEquals($tokens[0]['type'], $type);
    }

    public function testTokenizesJsonLiterals()
    {
        $l = new Lexer();
        $tokens = $l->tokenize('`null`, `false`, `true`, `"abc"`, `"ab\\"c"`,'
            . '`0`, `0.45`, `-0.5`');
        $this->assertNull($tokens[0]['value']);
        $this->assertFalse($tokens[2]['value']);
        $this->assertTrue($tokens[4]['value']);
        $this->assertEquals('abc', $tokens[6]['value']);
        $this->assertEquals('ab"c', $tokens[8]['value']);
        $this->assertSame(0, $tokens[10]['value']);
        $this->assertSame(0.45, $tokens[12]['value']);
        $this->assertSame(-0.5, $tokens[14]['value']);
    }

    public function testTokenizesJsonNumbers()
    {
        $l = new Lexer();
        $tokens = $l->tokenize('`10`, `1.2`, `-10.20e-10`, `1.2E+2`');
        $this->assertEquals(10, $tokens[0]['value']);
        $this->assertEquals(1.2, $tokens[2]['value']);
        $this->assertEquals(-1.02E-9, $tokens[4]['value']);
        $this->assertEquals(120, $tokens[6]['value']);
    }

    public function testCanWorkWithElidedJsonLiterals()
    {
        $l = new Lexer();
        $tokens = $l->tokenize('`foo`');
        $this->assertEquals('foo', $tokens[0]['value']);
        $this->assertEquals('literal', $tokens[0]['type']);
    }
}
