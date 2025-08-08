<?php

/**
 * ResponseHelper Utils tests using Pest
 */

use App\Utils\ResponseHelper;
use GuzzleHttp\Psr7\HttpFactory;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\Factory\DecoratedServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;

beforeEach(function () {
    $guzzleFactory = new HttpFactory();
    $responseFactory = new DecoratedResponseFactory($guzzleFactory, $guzzleFactory);
    $this->response = $responseFactory->createResponse();
});

describe('ResponseHelper::success', function () {
    it('creates success response with message', function () {
        $msg = 'Success message';

        $result = ResponseHelper::success($this->response, $msg);

        expect($result->getStatusCode())->toBe(200)
            ->and((string) $result->getBody())->toBe('{"ret":1,"msg":"Success message"}');
    });
});

describe('ResponseHelper::successWithData', function () {
    it('creates success response with message and data', function () {
        $msg = 'Success message';
        $data = ['foo' => 'bar'];

        $result = ResponseHelper::successWithData($this->response, $msg, $data);

        expect($result->getStatusCode())->toBe(200)
            ->and((string) $result->getBody())->toBe('{"ret":1,"msg":"Success message","data":{"foo":"bar"}}');
    });
});

describe('ResponseHelper::error', function () {
    it('creates error response with message', function () {
        $msg = 'Error message';

        $result = ResponseHelper::error($this->response, $msg);

        expect($result->getStatusCode())->toBe(200)
            ->and((string) $result->getBody())->toBe('{"ret":0,"msg":"Error message"}');
    });
});

describe('ResponseHelper::errorWithData', function () {
    it('creates error response with message and data', function () {
        $msg = 'Error message';
        $data = ['foo' => 'bar'];

        $result = ResponseHelper::errorWithData($this->response, $msg, $data);

        expect($result->getStatusCode())->toBe(200)
            ->and((string) $result->getBody())->toBe('{"ret":0,"msg":"Error message","data":{"foo":"bar"}}');
    });
});

describe('ResponseHelper::successWithDataEtag', function () {
    it('creates success response with data and ETag header', function () {
        $guzzleFactory = new HttpFactory();
        $requestFactory = new DecoratedServerRequestFactory($guzzleFactory);
        $request = $requestFactory->createServerRequest('GET', '/');
        
        $data = ['foo' => 'bar'];

        $result = ResponseHelper::successWithDataEtag($request, $this->response, $data);

        expect($result->getStatusCode())->toBe(200)
            ->and((string) $result->getBody())->toBe('{"ret":1,"data":{"foo":"bar"}}')
            ->and($result->getHeaderLine('ETag'))->toBe('W/"e929f5f04818d7ec"');
    });
});
