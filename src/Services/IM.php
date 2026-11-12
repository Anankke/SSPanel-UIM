<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\IM\Discord;
use App\Services\IM\ImType;
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
        return match (ImType::tryFrom($type)) {
            ImType::Slack => new Slack(),
            ImType::Discord => new Discord(),
            default => new Telegram(),
        };
    }

    /**
     * @throws GuzzleException
     * @throws TelegramSDKException
     */
    public static function send(string $to, string $msg, int $type): void
    {
        self::getClient($type)->send($to, $msg);
    }
}
