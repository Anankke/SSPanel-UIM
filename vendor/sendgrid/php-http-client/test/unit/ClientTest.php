<?php

namespace SendGrid\Test;

use SendGrid\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var MockClient */
    private $client;
    /** @var string */
    private $host;
    /** @var array */
    private $headers;

    protected function setUp()
    {
        $this->host = 'https://localhost:4010';
        $this->headers = [
            'Content-Type: application/json',
            'Authorization: Bearer SG.XXXX'
        ];
        $this->client = new MockClient($this->host, $this->headers, '/v3', null, null);
    }

    public function testConstructor()
    {
        $this->assertAttributeEquals($this->host, 'host', $this->client);
        $this->assertAttributeEquals($this->headers, 'headers', $this->client);
        $this->assertAttributeEquals('/v3', 'version', $this->client);
        $this->assertAttributeEquals([], 'path', $this->client);
        $this->assertAttributeEquals([], 'curlOptions', $this->client);
        $this->assertAttributeEquals(['delete', 'get', 'patch', 'post', 'put'], 'methods', $this->client);
    }

    public function test_()
    {
        $client = $this->client->_('test');
        $this->assertAttributeEquals(['test'], 'path', $client);
    }

    public function test__call()
    {
        $client = $this->client->get();
        $this->assertAttributeEquals('https://localhost:4010/v3/', 'url', $client);

        $queryParams = ['limit' => 100, 'offset' => 0];
        $client = $this->client->get(null, $queryParams);
        $this->assertAttributeEquals('https://localhost:4010/v3/?limit=100&offset=0', 'url', $client);

        $requestBody = ['name' => 'A New Hope'];
        $client = $this->client->get($requestBody);
        $this->assertAttributeEquals($requestBody, 'requestBody', $client);

        $requestHeaders = ['X-Mock: 200'];
        $client = $this->client->get(null, null, $requestHeaders);
        $this->assertAttributeEquals($requestHeaders, 'requestHeaders', $client);

        $client = $this->client->version('/v4');
        $this->assertAttributeEquals('/v4', 'version', $client);

        $client = $this->client->path_to_endpoint();
        $this->assertAttributeEquals(['path_to_endpoint'], 'path', $client);
        $client = $client->one_more_segment();
        $this->assertAttributeEquals(['path_to_endpoint', 'one_more_segment'], 'path', $client);
    }

    public function testGetHost()
    {
        $client = new Client('https://localhost:4010');
        $this->assertSame('https://localhost:4010', $client->getHost());
    }

    public function testGetHeaders()
    {
        $client = new Client('https://localhost:4010', ['Content-Type: application/json', 'Authorization: Bearer SG.XXXX']);
        $this->assertSame(['Content-Type: application/json', 'Authorization: Bearer SG.XXXX'], $client->getHeaders());

        $client2 = new Client('https://localhost:4010', null);
        $this->assertSame([], $client2->getHeaders());
    }

    public function testGetVersion()
    {
        $client = new Client('https://localhost:4010', null, '/v3');
        $this->assertSame('/v3', $client->getVersion());

        $client = new Client('https://localhost:4010', null, null);
        $this->assertSame(null, $client->getVersion());
    }

    public function testGetPath()
    {
        $client = new Client('https://localhost:4010', null, null, ['/foo/bar']);
        $this->assertSame(['/foo/bar'], $client->getPath());

        $client = new Client('https://localhost:4010', null, null, null);
        $this->assertSame([], $client->getPath());
    }

    public function testGetCurlOptions()
    {
        $client = new Client('https://localhost:4010', null, null, null, [CURLOPT_PROXY => '127.0.0.1:8080']);
        $this->assertSame([CURLOPT_PROXY => '127.0.0.1:8080'], $client->getCurlOptions());

        $client = new Client('https://localhost:4010', null, null, null, null);
        $this->assertSame([], $client->getCurlOptions());
    }
}