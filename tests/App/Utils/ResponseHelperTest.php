<?php

declare(strict_types=1);

namespace App\Utils;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\HttpFactory;
use Slim\Http\Factory\DecoratedResponseFactory;
use Psr\Http\Message\ServerRequestInterface;

class ResponseHelperTest extends TestCase
{
    /**
     * @covers App\Utils\ResponseHelper::success
     */
    public function testSuccess()
    {
        $guzzleFactory = new HttpFactory();
        $responseFactory = new DecoratedResponseFactory($guzzleFactory, $guzzleFactory);
        $response = $responseFactory->createResponse();

        $msg = 'Success message';

        $result = ResponseHelper::success($response, $msg);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('{"ret":1,"msg":"Success message"}', (string) $result->getBody());
    }

    /**
     * @covers App\Utils\ResponseHelper::successWithData
     */
    public function testSuccessWithData()
    {
        $guzzleFactory = new HttpFactory();
        $responseFactory = new DecoratedResponseFactory($guzzleFactory, $guzzleFactory);
        $response = $responseFactory->createResponse();

        $msg = 'Success message';
        $data = ['foo' => 'bar'];

        $result = ResponseHelper::successWithData($response, $msg, $data);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('{"ret":1,"msg":"Success message","data":{"foo":"bar"}}', (string) $result->getBody());
    }

    /**
     * @covers App\Utils\ResponseHelper::error
     */
    public function testError()
    {
        $guzzleFactory = new HttpFactory();
        $responseFactory = new DecoratedResponseFactory($guzzleFactory, $guzzleFactory);
        $response = $responseFactory->createResponse();

        $msg = 'Error message';

        $result = ResponseHelper::error($response, $msg);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('{"ret":0,"msg":"Error message"}', (string) $result->getBody());
    }

    /**
     * @covers App\Utils\ResponseHelper::errorWithData
     */
    public function testErrorWithData()
    {
        $guzzleFactory = new HttpFactory();
        $responseFactory = new DecoratedResponseFactory($guzzleFactory, $guzzleFactory);
        $response = $responseFactory->createResponse();

        $msg = 'Error message';
        $data = ['foo' => 'bar'];

        $result = ResponseHelper::errorWithData($response, $msg, $data);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('{"ret":0,"msg":"Error message","data":{"foo":"bar"}}', (string) $result->getBody());
    }

    /**
     * @covers App\Utils\ResponseHelper::successWithDataEtag
     * @throws Exception
     */
    public function testSuccessWithDataEtag()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getHeaderLine')->willReturn('');

        $guzzleFactory = new HttpFactory();
        $responseFactory = new DecoratedResponseFactory($guzzleFactory, $guzzleFactory);
        $response = $responseFactory->createResponse();
        $data = ['foo' => 'bar'];

        $result = ResponseHelper::successWithDataEtag($request, $response, $data);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('{"ret":1,"data":{"foo":"bar"}}', (string) $result->getBody());
        $this->assertEquals('W/"e929f5f04818d7ec"', $result->getHeaderLine('ETag'));
    }
}
