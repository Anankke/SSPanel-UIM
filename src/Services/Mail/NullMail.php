<?php

declare(strict_types=1);

namespace App\Services\Mail;

final class NullMail extends Base
{
    public function __construct()
    {
    }

    public function getConfig()
    {
        return [
        ];
    }

    public function send($to_address, $subject_raw, $text, $files): void
    {
        echo '';
    }
}
