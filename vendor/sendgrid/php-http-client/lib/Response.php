<?php

/**
 * HTTP Client library
 *
 * PHP version 5.4
 *
 * @author    Matt Bernier <dx@sendgrid.com>
 * @author    Elmer Thomas <dx@sendgrid.com>
 * @copyright 2016 SendGrid
 * @license   https://opensource.org/licenses/MIT The MIT License
 * @version   GIT: <git_id>
 * @link      http://packagist.org/packages/sendgrid/php-http-client
 */

namespace SendGrid;

/**
 * Holds the response from an API call.
 */
class Response
{
    /** @var int */
    protected $statusCode;
    /** @var string */
    protected $body;
    /** @var array */
    protected $headers;

    /**
     * Setup the response data
     *
     * @param int $statusCode the status code.
     * @param string $body    the response body.
     * @param array $headers  an array of response headers.
     */
    public function __construct($statusCode = null, $body = null, $headers = null)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * The status code
     *
     * @return int
     */
    public function statusCode()
    {
        return $this->statusCode;
    }

    /**
     * The response body
     *
     * @return string
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * The response headers
     *
     * @return array
     */
    public function headers()
    {
        return $this->headers;
    }
}