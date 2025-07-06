<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\IM\Discord;
use App\Services\IM\Slack;
use App\Services\IM\Telegram;
use GuzzleHttp\Exception\GuzzleException;
use Telegram\Bot\Exceptions\TelegramSDKException;

/*
 * IM Service
 */
final class IM
{
    public static function getClient(int $type): Discord|Slack|Telegram
    {
        return match ($type) {
            1 => new Discord(),
            2 => new Slack(),
            default => new Telegram(),
        };
    }

    /**
     * @throws GuzzleException
     * @throws TelegramSDKException
     */
    public static function send(int $to, string $msg, int $type): void
    {
        self::getClient($type)->send($to, $msg);
    }
}
