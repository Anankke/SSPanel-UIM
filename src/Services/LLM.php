<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Config;
use App\Services\LLM\Anthropic;
use App\Services\LLM\CloudflareWorkersAI;
use App\Services\LLM\GoogleAI;
use App\Services\LLM\HuggingFace;
use App\Services\LLM\OpenAI;
use App\Services\LLM\VertexAI;
use GuzzleHttp\Exception\GuzzleException;

final class LLM
{
    public static function getBackend(): VertexAI|CloudflareWorkersAI|GoogleAI|HuggingFace|OpenAI|Anthropic
    {
        return match (Config::obtain('llm_backend')) {
            'google-ai' => new GoogleAI(),
            'vertex-ai' => new VertexAI(),
            'huggingface' => new HuggingFace(),
            'cf-workers-ai' => new CloudflareWorkersAI(),
            'anthropic' => new Anthropic(),
            default => new OpenAI(),
        };
    }

    /**
     * @throws GuzzleException
     */
    public static function genTextResponse(string $q): string
    {
        if (Config::obtain('llm_backend') === '') {
            return 'No LLM backend configured';
        }

        if ($q === '') {
            return 'No question provided';
        }

        return self::getBackend()->textPrompt($q);
    }

    /**
     * @throws GuzzleException
     */
    public static function genTextResponseWithContext(string $q, array $context = []): string
    {
        if (Config::obtain('llm_backend') === '') {
            return 'No LLM backend configured';
        }

        if ($q === '') {
            return 'No question provided';
        }

        if ($context === []) {
            return self::getBackend()->textPrompt($q);
        }

        return self::getBackend()->textPromptWithContext($q, $context);
    }
}
