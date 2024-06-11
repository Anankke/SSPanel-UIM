<?php

declare(strict_types=1);

namespace App\Services\LLM;

use App\Models\Config;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class HuggingFace extends Base
{
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

        try {
            $response = json_decode($this->client->post(Config::obtain('huggingface_endpoint_url'), [
                'headers' => $headers,
                'json' => $data,
                'timeout' => 30,
            ])->getBody()->getContents());
        } catch (GuzzleException $e) {
            return '';
        }

        return $response->answer;
    }

    public function textPromptWithContext(array $context): string
    {
        return 'This service does not support context';
    }
}
