<?php

declare(strict_types=1);

namespace App\Services;

use App\Utils\Tools;
use function explode;
use function in_array;

final class Filter
{
    public static function checkEmailFilter(string $email): bool
    {
        if (! Tools::isEmail($email)) {
            return false;
        }

        $res = false;
        $mail_suffix = explode('@', $email)[1];
        $mail_filter = $_ENV['mail_filter'] ?? 0;
        $mail_filter_list = $_ENV['mail_filter_list'] ?? [];

        switch ($mail_filter) {
            case 1:
                // Whitelist
                if (in_array($mail_suffix, $mail_filter_list)) {
                    $res = true;
                }

                break;
            case 2:
                // Blacklist
                if (! in_array($mail_suffix, $mail_filter_list)) {
                    $res = true;
                }

                break;
            default:
                $res = true;
        }

        return $res;
    }
}
