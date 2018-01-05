<?php

namespace SendGrid\Test;

use SendGrid\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $response = new Response();
        
        $this->assertAttributeEquals(null, 'statusCode', $response);
        $this->assertAttributeEquals(null, 'body', $response);
        $this->assertAttributeEquals(null, 'headers', $response);

        $response = new Response(200, 'test', ['Content-Encoding: gzip']);

        $this->assertAttributeEquals(200, 'statusCode', $response);
        $this->assertAttributeEquals('test', 'body', $response);
        $this->assertAttributeEquals(['Content-Encoding: gzip'], 'headers', $response);
    }

    public function testStatusCode()
    {
        $response = new Response(404);

        $this->assertEquals(404, $response->statusCode());
    }

    public function testBody()
    {
        $response = new Response(null, 'foo');

        $this->assertEquals('foo', $response->body());
    }

    public function testHeaders()
    {
        $response = new Response(null, null, ['Content-Type: text/html']);

        $this->assertEquals(['Content-Type: text/html'], $response->headers());
    }
}