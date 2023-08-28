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
        return match ($_ENV['llm_backend']) {
            'openai' => self::textPromptGPT($q),
            'palm' => self::textPromptPaLM($q),
            'huggingface' => self::textPromptHF($q),
            default => 'No LLM backend configured',
        };
    }

    public static function textPromptGPT(string $q): string
    {
        if ($_ENV['openai_api_key'] === '') {
            return 'OpenAI API key not set';
        }

        if ($q === '') {
            return 'No question provided';
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
    public static function textPromptPaLM(string $q): string
    {
        if ($_ENV['palm_api_key'] === '') {
            return 'PaLM API key not set';
        }

        if ($q === '') {
            return 'No question provided';
        }

        $client = new Client();
        $api_url = 'https://generativelanguage.googleapis.com/v1beta2/models/' .
            $_ENV['palm_text_model'] . ':generateText?key=' . $_ENV['palm_api_key'];

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $data = [
            "prompt" => [
                "text" => $q,
            ],
            "temperature" => 1,
            "candidate_count" => 1,
            "top_k" => 40,
            "top_p" => 0.95,
            "max_output_tokens" => 1024,
            "stop_sequences" => [
            ],
            "safety_settings" => [
                [
                    "category" => "HARM_CATEGORY_DEROGATORY",
                    "threshold" => 3,
                ],
                [
                    "category" => "HARM_CATEGORY_TOXICITY",
                    "threshold" => 3,
                ],
                [
                    "category" => "HARM_CATEGORY_VIOLENCE",
                    "threshold" => 3,
                ],
                [
                    "category" => "HARM_CATEGORY_SEXUAL",
                    "threshold" => 3,
                ],
                [
                    "category" => "HARM_CATEGORY_MEDICAL",
                    "threshold" => 3,
                ],
                [
                    "category" => "HARM_CATEGORY_DANGEROUS",
                    "threshold" => 3,
                ],
            ],
        ];

        $response = json_decode($client->post($api_url, [
            'headers' => $headers,
            'json' => $data,
        ])->getBody()->getContents());

        return $response->candidates[0]->output;
    }

    /**
     * @throws GuzzleException
     */
    public static function textPromptHF(string $q): string
    {
        if ($_ENV['huggingface_api_key'] === '') {
            return 'Hugging Face API key not set';
        }

        if ($q === '') {
            return 'No question provided';
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
        ])->getBody()->getContents());

        return $response->answer;
    }
}
