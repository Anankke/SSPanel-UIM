<?php

declare(strict_types=1);

namespace App\Utils;

use App\Models\TelegramSession;
use function time;

final class TelegramSessionManager
{
    public static function generateRandomLink(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $token = Tools::genRandomChar(16);
            $Elink = TelegramSession::where('session_content', '=', $token)->first();
            if ($Elink === null) {
                return $token;
            }
        }

        return "couldn't alloc token";
    }

    public static function addBindSession($user)
    {
        $Elink = TelegramSession::where('type', '=', 0)->where('user_id', '=', $user->id)->first();
        if ($Elink !== null) {
            $Elink->datetime = time();
            $Elink->session_content = self::generateRandomLink();
            $Elink->save();
            return $Elink->session_content;
        }

        $NLink = new TelegramSession();
        $NLink->type = 0;
        $NLink->user_id = $user->id;
        $NLink->datetime = time();
        $NLink->session_content = self::generateRandomLink();
        $NLink->save();

        return $NLink->session_content;
    }

    public static function verifyBindSession($token)
    {
        $Elink = TelegramSession::where('type', '=', 0)->where('session_content', $token)->where('datetime', '>', time() - 600)->orderBy('datetime', 'desc')->first();
        if ($Elink !== null) {
            $uid = $Elink->user_id;
            $Elink->delete();
            return $uid;
        }
        return 0;
    }
}
