<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Services\Subscribe;

final class Json extends Base
{
    public function getContent($user): string
    {
        $sub_url = Subscribe::getUniversalSubLink($user);

        return json_encode([
            'version' => 4,
            'sub_name' => $_ENV['appName'],
            'email' => $user->email,
            'user_name' => $user->user_name,
            'class' => $user->class,
            'class_expire_date' => $user->class_expire,
            'total_traffic' => $user->transfer_enable,
            'used_upload_traffic' => $user->u,
            'used_download_traffic' => $user->d,
            'sub_url' => [
                'sing-box' => $sub_url . '/singbox',
                'clash' => $sub_url . '/clash',
            ],
        ]);
    }
}
