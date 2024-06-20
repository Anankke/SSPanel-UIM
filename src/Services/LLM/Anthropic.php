<?php

declare(strict_types=1);

namespace App\Services\LLM;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class Anthropic extends Base
{
    /**
     * @throws GuzzleException
     */
    public function textPrompt(string $q): string
    {
        if ($_ENV['anthropic_api_key'] === '') {
            return 'Anthropic API key not set';
        }

        $client = new Client();

        $api_url = 'https://api.anthropic.com/v1/messages';

        $headers = [
            'x-api-key' => $_ENV['anthropic_api_key'],
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ];

        $data = [
            'model' => $_ENV['anthropic_model_id'],
            'max_tokens' => 1024,
            'temperature' => 1,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $q,
                ],
            ],
        ];

        $response = json_decode($client->post($api_url, [
            'headers' => $headers,
            'json' => $data,
            'timeout' => 10,
        ])->getBody()->getContents());

        return $response->content[0]->text;
    }

    public function textPromptWithContext(string $q, array $context): string
    {
        return '';
    }
}
