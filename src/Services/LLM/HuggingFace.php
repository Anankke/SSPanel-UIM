<?php

declare(strict_types=1);

namespace App\Services\LLM;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class HuggingFace extends Base
{
    /**
     * @throws GuzzleException
     */
    public function textPrompt(string $q): string
    {
        if ($_ENV['huggingface_api_key'] === '' || $_ENV['huggingface_endpoint_url'] === '') {
            return 'Hugging Face API key or Endpoint URL not set';
        }

        $client = new Client();

        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['huggingface_api_key'],
            'Content-Type' => 'application/json',
        ];

        $data = [
            'inputs' => [
                'question' => $q,
            ],
        ];

        $response = json_decode($client->post($_ENV['huggingface_endpoint_url'], [
            'headers' => $headers,
            'json' => $data,
            'timeout' => 10,
        ])->getBody()->getContents());

        return $response->answer;
    }

    public function textPromptWithContext(string $q, array $context): string
    {
        return '';
    }
}
