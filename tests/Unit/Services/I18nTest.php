<?php

/**
 * I18n Service tests using Pest
 */

use App\Services\I18n;
use Symfony\Component\Translation\Translator;

require_once __DIR__ . '/../../../app/predefine.php';

describe('I18n::trans', function () {
    it('returns existing translation for valid key and locale', function () {
        $key = 'lang_name';
        $lang = 'en_US';
        $expectedTranslation = 'English(Simplified)';

        $translation = I18n::trans($key, $lang);

        expect($translation)->toBe($expectedTranslation);
    });

    it('returns key when translation does not exist', function () {
        $key = 'non_existent_key';
        $lang = 'en_US';

        $translation = I18n::trans($key, $lang);

        expect($translation)->toBe($key);
    });
});

describe('I18n::getLocaleList', function () {
    it('returns list of available locales', function () {
        $expectedLocales = ['en_US', 'ja_JP', 'zh_CN', 'zh_TW'];

        $locales = I18n::getLocaleList();

        expect($locales)->toBe($expectedLocales);
    });
});

describe('I18n::getTranslator', function () {
    it('returns translator instance with correct locale', function () {
        $lang = 'en_US';

        $translator = I18n::getTranslator($lang);

        expect($translator)
            ->toBeInstanceOf(Translator::class)
            ->and($translator->getLocale())->toBe($lang);
    });
});
