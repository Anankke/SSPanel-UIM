<?php

declare(strict_types=1);

namespace App\Services;

use OpenAI;
use function file_get_contents;
use function json_decode;
use function json_encode;
use function stream_context_create;

final class LLM
{
    public static function genTextResponse(string $q): string
    {
        return match ($_ENV['llm_backend']) {
            'openai' => self::textPromptGPT($q),
            'palm' => self::textPromptPaLM($q),
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

    public static function textPromptPaLM(string $q): string
    {
        if ($_ENV['palm_api_key'] === '') {
            return 'PaLM API key not set';
        }

        if ($q === '') {
            return 'No question provided';
        }

        $postdata = json_encode(
            [
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
            ]
        );

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $postdata,
                'timeout' => 5,
            ],
        ];

        $response = json_decode(file_get_contents(
            'https://generativelanguage.googleapis.com/v1beta2/models/' .
            $_ENV['palm_text_model'] .
            ':generateText?key=' .
            $_ENV['palm_api_key'],
            false,
            stream_context_create($opts)
        ));

        return $response->candidates[0]->output;
    }
}
