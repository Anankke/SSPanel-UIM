<?php
namespace GuzzleHttp\Tests\Psr7;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;

/**
 * @covers GuzzleHttp\Psr7\MessageTrait
 * @covers GuzzleHttp\Psr7\Response
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConstructor()
    {
        $r = new Response();
        $this->assertSame(200, $r->getStatusCode());
        $this->assertSame('1.1', $r->getProtocolVersion());
        $this->assertSame('OK', $r->getReasonPhrase());
        $this->assertSame([], $r->getHeaders());
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $r->getBody());
        $this->assertSame('', (string) $r->getBody());
    }

    public function testCanConstructWithStatusCode()
    {
        $r = new Response(404);
        $this->assertSame(404, $r->getStatusCode());
        $this->assertSame('Not Found', $r->getReasonPhrase());
    }

    public function testConstructorDoesNotReadStreamBody()
    {
        $streamIsRead = false;
        $body = Psr7\FnStream::decorate(Psr7\stream_for(''), [
            '__toString' => function () use (&$streamIsRead) {
                $streamIsRead = true;
                return '';
            }
        ]);

        $r = new Response(200, [], $body);
        $this->assertFalse($streamIsRead);
        $this->assertSame($body, $r->getBody());
    }

    public function testStatusCanBeNumericString()
    {
        $r = new Response('404');
        $r2 = $r->withStatus('201');
        $this->assertSame(404, $r->getStatusCode());
        $this->assertSame('Not Found', $r->getReasonPhrase());
        $this->assertSame(201, $r2->getStatusCode());
        $this->assertSame('Created', $r2->getReasonPhrase());
    }

    public function testCanConstructWithHeaders()
    {
        $r = new Response(200, ['Foo' => 'Bar']);
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame('Bar', $r->getHeaderLine('Foo'));
        $this->assertSame(['Bar'], $r->getHeader('Foo'));
    }

    public function testCanConstructWithHeadersAsArray()
    {
        $r = new Response(200, [
            'Foo' => ['baz', 'bar']
        ]);
        $this->assertSame(['Foo' => ['baz', 'bar']], $r->getHeaders());
        $this->assertSame('baz, bar', $r->getHeaderLine('Foo'));
        $this->assertSame(['baz', 'bar'], $r->getHeader('Foo'));
    }

    public function testCanConstructWithBody()
    {
        $r = new Response(200, [], 'baz');
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $r->getBody());
        $this->assertSame('baz', (string) $r->getBody());
    }

    public function testNullBody()
    {
        $r = new Response(200, [], null);
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $r->getBody());
        $this->assertSame('', (string) $r->getBody());
    }

    public function testFalseyBody()
    {
        $r = new Response(200, [], '0');
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $r->getBody());
        $this->assertSame('0', (string) $r->getBody());
    }

    public function testCanConstructWithReason()
    {
        $r = new Response(200, [], null, '1.1', 'bar');
        $this->assertSame('bar', $r->getReasonPhrase());

        $r = new Response(200, [], null, '1.1', '0');
        $this->assertSame('0', $r->getReasonPhrase(), 'Falsey reason works');
    }

    public function testCanConstructWithProtocolVersion()
    {
        $r = new Response(200, [], null, '1000');
        $this->assertSame('1000', $r->getProtocolVersion());
    }

    public function testWithStatusCodeAndNoReason()
    {
        $r = (new Response())->withStatus(201);
        $this->assertSame(201, $r->getStatusCode());
        $this->assertSame('Created', $r->getReasonPhrase());
    }

    public function testWithStatusCodeAndReason()
    {
        $r = (new Response())->withStatus(201, 'Foo');
        $this->assertSame(201, $r->getStatusCode());
        $this->assertSame('Foo', $r->getReasonPhrase());

        $r = (new Response())->withStatus(201, '0');
        $this->assertSame(201, $r->getStatusCode());
        $this->assertSame('0', $r->getReasonPhrase(), 'Falsey reason works');
    }

    public function testWithProtocolVersion()
    {
        $r = (new Response())->withProtocolVersion('1000');
        $this->assertSame('1000', $r->getProtocolVersion());
    }

    public function testSameInstanceWhenSameProtocol()
    {
        $r = new Response();
        $this->assertSame($r, $r->withProtocolVersion('1.1'));
    }

    public function testWithBody()
    {
        $b = Psr7\stream_for('0');
        $r = (new Response())->withBody($b);
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $r->getBody());
        $this->assertSame('0', (string) $r->getBody());
    }

    public function testSameInstanceWhenSameBody()
    {
        $r = new Response();
        $b = $r->getBody();
        $this->assertSame($r, $r->withBody($b));
    }

    public function testWithHeader()
    {
        $r = new Response(200, ['Foo' => 'Bar']);
        $r2 = $r->withHeader('baZ', 'Bam');
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar'], 'baZ' => ['Bam']], $r2->getHeaders());
        $this->assertSame('Bam', $r2->getHeaderLine('baz'));
        $this->assertSame(['Bam'], $r2->getHeader('baz'));
    }

    public function testWithHeaderAsArray()
    {
        $r = new Response(200, ['Foo' => 'Bar']);
        $r2 = $r->withHeader('baZ', ['Bam', 'Bar']);
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar'], 'baZ' => ['Bam', 'Bar']], $r2->getHeaders());
        $this->assertSame('Bam, Bar', $r2->getHeaderLine('baz'));
        $this->assertSame(['Bam', 'Bar'], $r2->getHeader('baz'));
    }

    public function testWithHeaderReplacesDifferentCase()
    {
        $r = new Response(200, ['Foo' => 'Bar']);
        $r2 = $r->withHeader('foO', 'Bam');
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['foO' => ['Bam']], $r2->getHeaders());
        $this->assertSame('Bam', $r2->getHeaderLine('foo'));
        $this->assertSame(['Bam'], $r2->getHeader('foo'));
    }

    public function testWithAddedHeader()
    {
        $r = new Response(200, ['Foo' => 'Bar']);
        $r2 = $r->withAddedHeader('foO', 'Baz');
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar', 'Baz']], $r2->getHeaders());
        $this->assertSame('Bar, Baz', $r2->getHeaderLine('foo'));
        $this->assertSame(['Bar', 'Baz'], $r2->getHeader('foo'));
    }

    public function testWithAddedHeaderAsArray()
    {
        $r = new Response(200, ['Foo' => 'Bar']);
        $r2 = $r->withAddedHeader('foO', ['Baz', 'Bam']);
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar', 'Baz', 'Bam']], $r2->getHeaders());
        $this->assertSame('Bar, Baz, Bam', $r2->getHeaderLine('foo'));
        $this->assertSame(['Bar', 'Baz', 'Bam'], $r2->getHeader('foo'));
    }

    public function testWithAddedHeaderThatDoesNotExist()
    {
        $r = new Response(200, ['Foo' => 'Bar']);
        $r2 = $r->withAddedHeader('nEw', 'Baz');
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar'], 'nEw' => ['Baz']], $r2->getHeaders());
        $this->assertSame('Baz', $r2->getHeaderLine('new'));
        $this->assertSame(['Baz'], $r2->getHeader('new'));
    }

    public function testWithoutHeaderThatExists()
    {
        $r = new Response(200, ['Foo' => 'Bar', 'Baz' => 'Bam']);
        $r2 = $r->withoutHeader('foO');
        $this->assertTrue($r->hasHeader('foo'));
        $this->assertSame(['Foo' => ['Bar'], 'Baz' => ['Bam']], $r->getHeaders());
        $this->assertFalse($r2->hasHeader('foo'));
        $this->assertSame(['Baz' => ['Bam']], $r2->getHeaders());
    }

    public function testWithoutHeaderThatDoesNotExist()
    {
        $r = new Response(200, ['Baz' => 'Bam']);
        $r2 = $r->withoutHeader('foO');
        $this->assertSame($r, $r2);
        $this->assertFalse($r2->hasHeader('foo'));
        $this->assertSame(['Baz' => ['Bam']], $r2->getHeaders());
    }

    public function testSameInstanceWhenRemovingMissingHeader()
    {
        $r = new Response();
        $this->assertSame($r, $r->withoutHeader('foo'));
    }

    public function testHeaderValuesAreTrimmed()
    {
        $r1 = new Response(200, ['OWS' => " \t \tFoo\t \t "]);
        $r2 = (new Response())->withHeader('OWS', " \t \tFoo\t \t ");
        $r3 = (new Response())->withAddedHeader('OWS', " \t \tFoo\t \t ");;

        foreach ([$r1, $r2, $r3] as $r) {
            $this->assertSame(['OWS' => ['Foo']], $r->getHeaders());
            $this->assertSame('Foo', $r->getHeaderLine('OWS'));
            $this->assertSame(['Foo'], $r->getHeader('OWS'));
        }
    }
}
