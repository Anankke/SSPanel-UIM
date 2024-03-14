<?php

declare(strict_types=1);

namespace App\Services\LLM;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class GoogleAI extends Base
{
    /**
     * @throws GuzzleException
     */
    public function textPrompt(string $q): string
    {
        if ($_ENV['google_ai_api_key'] === '') {
            return 'Google AI API key not set';
        }

        $client = new Client();

        $api_url = 'https://generativelanguage.googleapis.com/v1/models/' .
            $_ENV['google_ai_model_id'] . ':generateContent?key=' . $_ENV['google_ai_api_key'];

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $data = [
            'contents' => [
                'parts' => [
                    [
                        'text' => $q,
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 1,
                'topK' => 1,
                'topP' => 1,
                'candidateCount' => 1,
                'maxOutputTokens' => 2048,
                'stopSequences' => [],
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_NONE',
                ],
            ],
        ];

        $response = json_decode($client->post($api_url, [
            'headers' => $headers,
            'json' => $data,
            'timeout' => 10,
        ])->getBody()->getContents());

        return $response->candidates[0]->content->parts[0]->text;
    }

    public function textPromptWithContext(string $q, array $context): string
    {
        return '';
    }
}
