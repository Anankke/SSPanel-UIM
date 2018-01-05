<?php
namespace JmesPath\Tests;

use JmesPath\SyntaxErrorException;

/**
 * @covers JmesPath\SyntaxErrorException
 */
class SyntaxErrorExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatesWithNoArray()
    {
        $e = new SyntaxErrorException(
            'Found comma',
            ['type' => 'comma', 'pos' => 3, 'value' => ','],
            'abc,def'
        );
        $expected = <<<EOT
Syntax error at character 3
abc,def
   ^
Found comma
EOT;
        $this->assertContains($expected, $e->getMessage());
    }

    public function testCreatesWithArray()
    {
        $e = new SyntaxErrorException(
            ['dot' => true, 'eof' => true],
            ['type' => 'comma', 'pos' => 3, 'value' => ','],
            'abc,def'
        );
        $expected = <<<EOT
Syntax error at character 3
abc,def
   ^
Expected one of the following: dot, eof; found comma ","
EOT;
        $this->assertContains($expected, $e->getMessage());
    }
}
