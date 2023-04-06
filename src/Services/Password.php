<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PasswordReset;
use App\Utils\Tools;
use Psr\Http\Client\ClientExceptionInterface;
use function time;

final class Password
{
    /**
     * @throws ClientExceptionInterface
     */
    public static function sendResetEmail($email): void
    {
        $pwdRst = new PasswordReset();
        $pwdRst->email = $email;
        $pwdRst->init_time = time();
        $pwdRst->expire_time = time() + 3600 * 24;
        $pwdRst->token = Tools::genRandomChar(64);
        $pwdRst->save();

        $subject = $_ENV['appName'] . '重置密码';
        $resetUrl = $_ENV['baseUrl'] . '/password/token/' . $pwdRst->token;

        Mail::send(
            $email,
            $subject,
            'password/reset.tpl',
            [
                'resetUrl' => $resetUrl,
            ]
        );
    }
}
