<?php

declare(strict_types=1);

namespace App\Services\LLM;

use App\Models\Config;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class Anthropic extends Base
{
    /**
     * @throws GuzzleException
     */
    public function textPrompt(string $q): string
    {
        if (Config::obtain('anthropic_api_key') === '') {
            return 'Anthropic API key not set';
        }

        $api_url = 'https://api.anthropic.com/v1/messages';

        $headers = [
            'x-api-key' => Config::obtain('anthropic_api_key'),
            'content-type' => 'application/json',
        ];

        $data = [
            'model' => Config::obtain('anthropic_model_id'),
            'max_tokens' => 1024,
            'temperature' => 1,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $q,
                ],
            ],
        ];

        $response = json_decode($this->client->post($api_url, [
            'headers' => $headers,
            'json' => $data,
            'timeout' => 30,
        ])->getBody()->getContents());

        return $response->content[0]->text;
    }

    public function textPromptWithContext(string $q, array $context): string
    {
        return '';
    }
}
