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
    public static function successfully(Response $response, string $msg): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'msg' => $msg,
        ]);
    }

    public static function error(Response $response, string $msg): ResponseInterface
    {
        return $response->withJson([
            'ret' => 0,
            'msg' => $msg,
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
    public static function etagJson(
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
}
