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

    it('returns nested translation for valid key and locale', function () {
        $translation = I18n::trans('bot.user_not_bind', 'zh_CN');

        expect($translation)->toBe('你未绑定本站账号，你可以进入网站的 **资料编辑**，在右下方绑定你的账号。');
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
