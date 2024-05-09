<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Translator;
use const BASE_PATH;

final class I18n
{
    // trans() right is human right 🏳️‍⚧️
    public static function trans(string $key, string $lang = 'en_US'): string
    {
        $translator = self::getTranslator($lang);

        return $translator->trans($key);
    }

    public static function getLocaleList(): array
    {
        return [
            'en_US',
            'ja_JP',
            'zh_CN',
            'zh_TW',
        ];
    }

    public static function getTranslator($lang = 'en_US'): Translator
    {
        $translator = new Translator($lang);
        $translator->addLoader('php', new PhpFileLoader());
        $translator->addResource(
            'php',
            BASE_PATH . '/resources/locale/' . $lang . '.php',
            $lang
        );

        return $translator;
    }
}
