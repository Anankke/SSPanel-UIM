<?php
namespace JmesPath\Tests\Tree;

use JmesPath\AstRuntime;
use JmesPath\TreeInterpreter;

/**
 * @covers JmesPath\Tree\TreeInterpreter
 */
class TreeInterpreterTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsNullWhenMergingNonArray()
    {
        $t = new TreeInterpreter();
        $this->assertNull($t->visit(array(
            'type' => 'flatten',
            'children' => array(
                array('type' => 'literal', 'value' => 1),
                array('type' => 'literal', 'value' => 1)
            )
        ), array(), array(
            'runtime' => new AstRuntime()
        )));
    }

    public function testWorksWithArrayObjectAsObject()
    {
        $runtime = new AstRuntime();
        $this->assertEquals('baz', $runtime('foo.bar', new \ArrayObject([
            'foo' => new \ArrayObject(['bar' => 'baz'])
        ])));
    }

    public function testWorksWithArrayObjectAsArray()
    {
        $runtime = new AstRuntime();
        $this->assertEquals('baz', $runtime('foo[0].bar', new \ArrayObject([
            'foo' => new \ArrayObject([new \ArrayObject(['bar' => 'baz'])])
        ])));
    }

    public function testWorksWithArrayProjections()
    {
        $runtime = new AstRuntime();
        $this->assertEquals(
            ['baz'],
            $runtime('foo[*].bar', new \ArrayObject([
                'foo' => new \ArrayObject([
                    new \ArrayObject([
                        'bar' => 'baz'
                    ])
                ])
            ]))
        );
    }

    public function testWorksWithObjectProjections()
    {
        $runtime = new AstRuntime();
        $this->assertEquals(
            ['baz'],
            $runtime('foo.*.bar', new \ArrayObject([
                'foo' => new \ArrayObject([
                    'abc' => new \ArrayObject([
                        'bar' => 'baz'
                    ])
                ])
            ]))
        );
    }
}
