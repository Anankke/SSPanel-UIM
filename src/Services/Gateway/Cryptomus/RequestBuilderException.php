<?php

declare(strict_types=1);

namespace App\Services\Gateway\Cryptomus;

final class RequestBuilderException extends \Exception
{
    private string $method;
    private array $errors;

    public function __construct(string $message, int $responseCode, string $uri, array $errors = [], mixed $previous = null)
    {
        $this->method = $uri;
        $this->errors = $errors;

        parent::__construct($message, $responseCode, $previous);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
