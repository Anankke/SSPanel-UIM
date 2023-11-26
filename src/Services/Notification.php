<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailQueue;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientExceptionInterface;
use Telegram\Bot\Exceptions\TelegramSDKException;

final class Notification
{
    /**
     * @throws GuzzleException
     * @throws TelegramSDKException
     * @throws ClientExceptionInterface
     */
    public static function notifyAdmin($title = '', $msg = '', $template = 'warn.tpl'): void
    {
        $admins = (new User())->where('is_admin', 1)->get();

        foreach ($admins as $admin) {
            if ($admin->contact_method === 1 || $admin->im_type === 0) {
                Mail::send(
                    $admin->email,
                    $title,
                    $template,
                    [
                        'user' => $admin,
                        'title' => $title,
                        'text' => $msg,
                    ]
                );
            } else {
                IM::send($admin->im_value, $msg, $admin->im_type);
            }
        }
    }

    /**
     * @throws GuzzleException
     * @throws TelegramSDKException
     * @throws ClientExceptionInterface
     */
    public static function notifyUser($user, $title = '', $msg = '', $template = 'warn.tpl'): void
    {
        if ($user->contact_method === 1 || $user->im_type === 0) {
            $array = [
                'user' => $user,
                'title' => $title,
                'text' => $msg,
            ];

            (new EmailQueue())->add($user->email, $title, $template, $array);
        } else {
            IM::send($user->im_value, $msg, $user->im_type);
        }
    }

    /**
     * @throws GuzzleException
     * @throws TelegramSDKException
     */
    public static function notifyAllUser($title = '', $msg = '', $template = 'warn.tpl'): void
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->contact_method === 1 || $user->im_type === 0) {
                $array = [
                    'user' => $user,
                    'title' => $title,
                    'text' => $msg,
                ];

                (new EmailQueue())->add($user->email, $title, $template, $array);
            } else {
                IM::send($user->im_value, $msg, $user->im_type);
            }
        }
    }
}
