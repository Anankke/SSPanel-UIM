<?php

declare(strict_types=1);

namespace App\Services\IM;

// The values are stored in user.im_type and must never be renumbered.
enum ImType: int
{
    case None = 0;
    case Slack = 1;
    case Discord = 2;
    case Telegram = 4;

    public static function map(): array
    {
        $map = [];

        foreach (self::cases() as $case) {
            $map[$case->name] = $case->value;
        }

        return $map;
    }
}
