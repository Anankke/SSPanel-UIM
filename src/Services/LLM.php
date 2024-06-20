<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\LLM\Anthropic;
use App\Services\LLM\CloudflareWorkersAI;
use App\Services\LLM\GoogleAI;
use App\Services\LLM\HuggingFace;
use App\Services\LLM\OpenAI;
use App\Services\LLM\VertexAI;

final class LLM
{
    public static function getBackend(): VertexAI|CloudflareWorkersAI|GoogleAI|HuggingFace|OpenAI|Anthropic
    {
        return match ($_ENV['llm_backend']) {
            'google-ai' => new GoogleAI(),
            'vertex-ai' => new VertexAI(),
            'huggingface' => new HuggingFace(),
            'cf-workers-ai' => new CloudflareWorkersAI(),
            'anthropic' => new Anthropic(),
            default => new OpenAI(),
        };
    }

    public static function genTextResponse(string $q): string
    {
        if ($q === '') {
            return 'No question provided';
        }

        return self::getClient()->textPrompt($q);
    }

    public static function genTextResponseWithContext(string $q, array $context = []): string
    {
        if ($q === '') {
            return 'No question provided';
        }

        if ($context === []) {
            return self::getClient()->textPrompt($q);
        }

        return self::getClient()->textPromptWithContext($q, $context);
    }
}
