<?php

declare(strict_types=1);

namespace App\Utils;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use function hash;
use function json_encode;

final class ResponseHelper
{
    public static function success(Response $response, string $msg = ''): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'msg' => $msg,
        ]);
    }

    /**
     * @param Response $response
     * @param string $msg
     * @param array $data
     *
     * @return ResponseInterface
     */
    public static function successWithData(Response $response, string $msg = '', array $data = []): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    /**
     * Build a JSON response with ETag header.
     *
     * **Note**: `RequestInterface` or `ResponseInterface` shouldn't be modified before/after calling this function.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param mixed $data
     *
     * @return ResponseInterface
     */
    public static function successWithDataEtag(
        RequestInterface $request,
        ResponseInterface $response,
        mixed $data
    ): ResponseInterface {
        $etag = 'W/"' . hash('xxh64', (string) json_encode($data)) . '"';

        if ($etag === $request->getHeaderLine('If-None-Match')) {
            return $response->withStatus(304);
        }

        return $response->withHeader('ETag', $etag)->withJson($data);
    }

    public static function error(Response $response, string $msg = ''): ResponseInterface
    {
        return $response->withJson([
            'ret' => 0,
            'msg' => $msg,
        ]);
    }

    /**
     * @param Response $response
     * @param string $msg
     * @param array $data
     *
     * @return ResponseInterface
     */
    public static function errorWithData(Response $response, string $msg = '', array $data = []): ResponseInterface
    {
        return $response->withJson([
            'ret' => 0,
            'msg' => $msg,
            'data' => $data,
        ]);
    }
}
