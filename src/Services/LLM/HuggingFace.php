<?php

declare(strict_types=1);

namespace App\Services\LLM;

use App\Models\Config;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class HuggingFace extends Base
{
    /**
     * @throws GuzzleException
     */
    public function textPrompt(string $q): string
    {
        if (Config::obtain('huggingface_api_key') === '' || Config::obtain('huggingface_endpoint_url') === '') {
            return 'Hugging Face API key or Endpoint URL not set';
        }

        $headers = [
            'Authorization' => 'Bearer ' . Config::obtain('huggingface_api_key'),
            'Content-Type' => 'application/json',
        ];

        $data = [
            'inputs' => [
                'question' => $q,
            ],
        ];

        $response = json_decode($this->client->post(Config::obtain('huggingface_endpoint_url'), [
            'headers' => $headers,
            'json' => $data,
            'timeout' => 30,
        ])->getBody()->getContents());

        return $response->answer;
    }

    public function textPromptWithContext(string $q, array $context): string
    {
        return '';
    }
}
