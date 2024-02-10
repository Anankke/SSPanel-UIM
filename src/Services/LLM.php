<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OpenAI;
use function json_decode;

final class LLM
{
    /**
     * @throws GuzzleException
     */
    public static function genTextResponse(string $q): string
    {
        if ($q === '') {
            return 'No question provided';
        }

        return match ($_ENV['llm_backend']) {
            'openai' => self::textPromptGPT($q),
            'google-ai' => self::textPromptGoogleAI($q),
            'vertex-ai' => self::textPromptVertexAI($q),
            'huggingface' => self::textPromptHF($q),
            'cf-workers-ai' => self::textPromptCF($q),
            default => 'No LLM backend configured',
        };
    }

    public static function textPromptGPT(string $q): string
    {
        if ($_ENV['openai_api_key'] === '') {
            return 'OpenAI API key not set';
        }

        $client = OpenAI::client($_ENV['openai_api_key']);

        $response = $client->chat()->create([
            'model' => $_ENV['openai_model'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $q,
                ],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    /**
     * @throws GuzzleException
     */
    public static function textPromptGoogleAI(string $q): string
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

    /**
     * @throws GuzzleException
     */
    public static function textPromptVertexAI(string $q): string
    {
        if ($_ENV['vertex_ai_access_token'] === '') {
            return 'Vertex AI API key not set';
        }

        $client = new Client();

        $api_url = 'https://' . $_ENV['vertex_ai_location'] .'-aiplatform.googleapis.com/v1/projects/' .
            $_ENV['vertex_ai_project_id'] . '/locations/' . $_ENV['vertex_ai_location'] . '/publishers/google/models/' .
            $_ENV['vertex_ai_model_id'] . ':streamGenerateContent';

        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['vertex_ai_access_token'],
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

    /**
     * @throws GuzzleException
     */
    public static function textPromptHF(string $q): string
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

    /**
     * @throws GuzzleException
     */
    public static function textPromptCF(string $q): string
    {
        if ($_ENV['cf_workers_ai_account_id'] === '' || $_ENV['cf_workers_ai_api_token'] === '') {
            return 'Cloudflare Workers AI Account ID or API Token not set';
        }

        $client = new Client();

        $api_url = 'https://api.cloudflare.com/client/v4/accounts/' .
            $_ENV['cf_workers_ai_account_id'] . '/ai/run/' . $_ENV['cf_workers_ai_model_id'];

        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['cf_workers_ai_api_token'],
            'Content-Type' => 'application/json',
        ];

        $data = [
            'prompt' => $q,
        ];

        $response = json_decode($client->post($api_url, [
            'headers' => $headers,
            'json' => $data,
            'timeout' => 10,
        ])->getBody()->getContents());

        return $response->result->response;
    }
}
