<?php

declare(strict_types=1);

namespace App\Services\LLM;

abstract class Base
{
    abstract public function textPrompt(string $q): string;

    abstract public function textPromptWithContext(string $q, array $context): string;
}
