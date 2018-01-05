<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/codeguy/Slim
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/codeguy/Slim/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Views;

use Slim\Views\Twig;

require dirname(__DIR__) . '/vendor/autoload.php';

class TwigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Twig
     */
    protected $view;

    public function setUp()
    {
        $this->view = new Twig(dirname(__FILE__) . '/templates');
    }

    public function testFetch()
    {
        $output = $this->view->fetch('example.html', [
            'name' => 'Josh'
        ]);

        $this->assertEquals("<p>Hi, my name is Josh.</p>\n", $output);
    }

    public function testRender()
    {
        $mockBody = $this->getMockBuilder('Psr\Http\Message\StreamInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockBody->expects($this->once())
            ->method('write')
            ->with("<p>Hi, my name is Josh.</p>\n")
            ->willReturn(28);

        $mockResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($mockBody);

        $response = $this->view->render($mockResponse, 'example.html', [
            'name' => 'Josh'
        ]);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }
}
