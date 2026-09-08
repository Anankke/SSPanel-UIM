<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use function basename;
use function glob;
use function is_array;
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
        $locales = [];
        $files = glob(BASE_PATH . '/resources/locale/*.php');

        foreach ($files as $file) {
            $locales[] = basename($file, '.php');
        }

        return $locales;
    }

    public static function getTranslator($lang = 'en_US'): Translator
    {
        $translator = new Translator($lang);
        $translator->addLoader('array', new ArrayLoader());
        $messages = require BASE_PATH . '/resources/locale/' . $lang . '.php';

        $translator->addResource(
            'array',
            self::flattenMessages($messages),
            $lang
        );

        return $translator;
    }

    private static function flattenMessages(array $messages, string $prefix = ''): array
    {
        $flattened = [];

        foreach ($messages as $key => $value) {
            $messageId = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $flattened += self::flattenMessages($value, $messageId);
                continue;
            }

            $flattened[$messageId] = (string) $value;
        }

        return $flattened;
    }
}
