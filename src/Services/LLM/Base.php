<?php

declare(strict_types=1);

namespace App\Services\LLM;

use GuzzleHttp\Client;

abstract class Base
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    abstract public function textPrompt(string $q): string;

    abstract public function textPromptWithContext(array $context): string;
}
