<?php

declare(strict_types=1);

namespace Tests;

use Slim\Factory\AppFactory;
use Slim\Http\Factory\DecoratedServerRequestFactory;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class SlimTestCase extends TestCase
{
    protected $app;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->setUpTestEnvironment();
        
        $this->app = AppFactory::create();
        
        $routes = require BASE_PATH . '/app/routes.php';
        $routes($this->app);
        
        $this->app->addErrorMiddleware(true, true, true);
    }
    
    protected function setUpTestEnvironment(): void
    {
        $_ENV['db_driver'] = 'sqlite';
        $_ENV['db_database'] = ':memory:';
        
        $_ENV['webAPI'] = true;
        $_ENV['muKey'] = 'test_key_123';
        $_ENV['webAPIUrl'] = 'https://test.example.com';
        $_ENV['checkNodeIp'] = false;
        $_ENV['enable_rate_limit'] = false;
        $_ENV['timeZone'] = 'UTC';
        
        // Email Settings
        $_ENV['enable_email_verify'] = false;
        
        // Subscription Settings
        $_ENV['Subscribe'] = true;
        $_ENV['subUrl'] = 'https://test.example.com';
        
        $_ENV['appName'] = 'SSPanel Test';
        $_ENV['baseUrl'] = 'https://test.example.com';
        $_ENV['key'] = 'test_encryption_key_123456789012';
        $_ENV['debug'] = true;
        $_ENV['enable_kill'] = false;
        $_ENV['enable_change_email'] = true;
    }
    
    protected function createRequest(
        string $method,
        string $uri,
        array $serverParams = []
    ): ServerRequestInterface {
        $defaults = [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $uri,
            'SERVER_NAME' => 'test.example.com',
            'SERVER_PORT' => 443,
            'HTTPS' => 'on',
            'HTTP_HOST' => 'test.example.com',
            'REMOTE_ADDR' => '127.0.0.1',
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
        ];
        
        $serverParams = array_merge($defaults, $serverParams);
        
        $psr7Factory = new HttpFactory();
        $factory = new DecoratedServerRequestFactory($psr7Factory);
        
        $request = $factory->createServerRequest($method, $uri, $serverParams);
        if (!$request->hasHeader('Host')) {
            $request = $request->withHeader('Host', $serverParams['HTTP_HOST']);
        }
        
        return $request;
    }
    
    protected function json(
        string $method,
        string $uri,
        array $data = [],
        array $headers = []
    ): ResponseInterface {
        $request = $this->createRequest($method, $uri);
        
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withHeader('Accept', 'application/json');
        
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        
        if (!empty($data)) {
            $request = $request->withParsedBody($data);
        }
        
        return $this->app->handle($request);
    }
    
    protected function get(string $uri, array $headers = []): ResponseInterface
    {
        $request = $this->createRequest('GET', $uri);
        
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        
        return $this->app->handle($request);
    }
    
    protected function post(string $uri, array $data = [], array $headers = []): ResponseInterface
    {
        return $this->json('POST', $uri, $data, $headers);
    }
    
    protected function put(string $uri, array $data = [], array $headers = []): ResponseInterface
    {
        return $this->json('PUT', $uri, $data, $headers);
    }
    
    protected function delete(string $uri, array $headers = []): ResponseInterface
    {
        return $this->json('DELETE', $uri, [], $headers);
    }
    
    protected function assertJsonResponse(ResponseInterface $response): array
    {
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        
        $body = (string) $response->getBody();
        $data = json_decode($body, true);
        
        $this->assertIsArray($data);
        
        return $data;
    }
    
    protected function assertSuccess(ResponseInterface $response): void
    {
        $status = $response->getStatusCode();
        $this->assertGreaterThanOrEqual(200, $status);
        $this->assertLessThan(300, $status);
    }
    
    protected function assertError(ResponseInterface $response, ?int $expectedStatus = null): void
    {
        $status = $response->getStatusCode();
        
        if ($expectedStatus !== null) {
            $this->assertEquals($expectedStatus, $status);
        } else {
            $this->assertGreaterThanOrEqual(400, $status);
        }
    }
}