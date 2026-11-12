<?php

declare(strict_types=1);

use App\Command\Tool;
use App\Models\Config;
use App\Models\User;
use App\Services\DB;
use App\Services\View;

function publicSettingImport(): void
{
    ob_start();
    (new Tool(['xcat', 'Tool', 'importSetting']))->importSetting();
    ob_end_clean();
}

function publicSettingFileItems(): array
{
    $items = [];

    foreach (json_decode(file_get_contents(BASE_PATH . '/config/settings.json'), true) as $row) {
        $items[$row['item']] = $row;
    }

    return $items;
}

beforeEach(function () {
    DB::connection()->beginTransaction();

    $this->previousUser = $GLOBALS['user'] ?? null;
    $user = new User();
    $user->isLogin = true;
    $user->theme = 'tabler';
    $GLOBALS['user'] = $user;
});

afterEach(function () {
    $GLOBALS['user'] = $this->previousUser;
    DB::connection()->rollBack();
});

describe('public settings in views', function () {
    it('gives views exactly the settings marked public in settings.json', function () {
        publicSettingImport();
        $public = array_keys(array_filter(
            publicSettingFileItems(),
            static fn (array $row): bool => $row['is_public'] === 1
        ));

        expect(array_keys(View::getSmarty()->getTemplateVars('public_setting')))
            ->toEqualCanonicalizing($public)
            ->and(array_keys(View::getTwig()->getGlobals()['public_setting']))
            ->toEqualCanonicalizing($public);
    });

    it('does not give views the Cryptomus API key and merchant UUID', function () {
        publicSettingImport();
        Config::set('cryptomus_api_key', 'test-api-key');
        Config::set('cryptomus_uuid', 'test-merchant-uuid');

        expect(View::getSmarty()->getTemplateVars('public_setting'))
            ->not->toHaveKey('cryptomus_api_key')
            ->not->toHaveKey('cryptomus_uuid')
            ->toHaveKeys(['paypal_client_id', 'captcha_provider', 'telegram_bot', 'livechat_license']);
    });
});

describe('Tool importSetting', function () {
    it('syncs class and is_public from settings.json and keeps stored values', function () {
        publicSettingImport();
        $file = publicSettingFileItems();

        foreach (Config::all() as $row) {
            $row->is_public = 1 - $file[$row->item]['is_public'];
            $row->class = 'other';
            $row->value = 'site-value-' . $row->item;
            $row->save();
        }

        publicSettingImport();
        $rows = Config::all();

        expect($rows)->toHaveCount(count($file));

        foreach ($rows as $row) {
            expect((int) $row->is_public)->toBe($file[$row->item]['is_public'])
                ->and($row->class)->toBe($file[$row->item]['class'])
                ->and($row->value)->toBe('site-value-' . $row->item);
        }
    });
});
