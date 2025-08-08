<?php

use App\Models\User;
use App\Services\View;

beforeEach(function () {
    $this->originalEnv = $_ENV;
    $this->view = new View();
    $this->user = new User();
});

afterEach(function () {
    $_ENV = $this->originalEnv;
});

describe('View::getTheme', function () {
    it('returns user theme when user is logged in', function () {
        $this->user->isLogin = true;
        $this->user->theme = 'tabler';

        $theme = $this->view->getTheme($this->user);

        expect($theme)->toBe('tabler');
    });

    it('returns default theme when user is not logged in', function () {
        $_ENV['theme'] = 'not-tabler';
        $this->user->isLogin = false;

        $theme = $this->view->getTheme($this->user);

        expect($theme)->toBe('not-tabler');
    });
});

describe('View::getConfig', function () {
    it('returns complete configuration array', function () {
        $_ENV['appName'] = 'Test App';
        $_ENV['baseUrl'] = 'http://localhost';
        $_ENV['jump_delay'] = 1000;
        $_ENV['enable_kill'] = true;
        $_ENV['enable_change_email'] = true;
        $_ENV['enable_r2_client_download'] = true;
        $_ENV['jsdelivr_url'] = 'https://cdn.jsdelivr.net';
        $_ENV['locale'] = 'en_US';

        $config = $this->view->getConfig();

        expect($config)
            ->toBeArray()
            ->toHaveKeys([
                'appName', 'baseUrl', 'jump_delay', 'enable_kill',
                'enable_change_email', 'enable_r2_client_download', 'jsdelivr_url', 'locale'
            ])
            ->and($config['appName'])->toBe('Test App')
            ->and($config['baseUrl'])->toBe('http://localhost')
            ->and($config['jump_delay'])->toBe(1000)
            ->and($config['enable_kill'])->toBeTrue()
            ->and($config['enable_change_email'])->toBeTrue()
            ->and($config['enable_r2_client_download'])->toBeTrue()
            ->and($config['jsdelivr_url'])->toBe('https://cdn.jsdelivr.net')
            ->and($config['locale'])->toBe('en_US');
    });
});
