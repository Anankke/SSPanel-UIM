<?php

declare(strict_types=1);

namespace App\Services\Mail;

final class NullMail extends Base
{
    public function __construct()
    {
    }

    public function getConfig(): array
    {
        return [
        ];
    }

    public function send($to, $subject, $body): void
    {
        echo '';
    }
}
