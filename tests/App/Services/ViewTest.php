<?php

declare(strict_types=1);

namespace App\Services;

use PHPUnit\Framework\TestCase;
use App\Services\View;
use App\Models\User;

final class ViewTest extends TestCase
{
    private View $view;
    private User $user;

    protected function setUp(): void
    {
        $this->view = new View();
        $this->user = new User();
    }

    /**
     * @covers App\Services\View::getTheme
     */
    public function testGetTheme(): void
    {
        $this->user->isLogin = true;
        $this->user->theme = 'tabler';

        $theme = $this->view->getTheme($this->user);

        $this->assertEquals('tabler', $theme);

        $_ENV['theme'] = 'not-tabler';
        $this->user->isLogin = false;

        $theme = $this->view->getTheme($this->user);

        $this->assertEquals('not-tabler', $theme);
    }

    /**
     * @covers App\Services\View::getConfig
     */
    public function testGetConfig(): void
    {
        $_ENV['appName'] = 'Test App';
        $_ENV['baseUrl'] = 'http://localhost';
        $_ENV['jump_delay'] = 3;
        $_ENV['enable_kill'] = true;
        $_ENV['enable_change_email'] = true;
        $_ENV['enable_r2_client_download'] = true;
        $_ENV['jsdelivr_url'] = 'https://cdn.jsdelivr.net';

        $config = $this->view->getConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('appName', $config);
        $this->assertArrayHasKey('baseUrl', $config);
        $this->assertArrayHasKey('jump_delay', $config);
        $this->assertArrayHasKey('enable_kill', $config);
        $this->assertArrayHasKey('enable_change_email', $config);
        $this->assertArrayHasKey('enable_r2_client_download', $config);
        $this->assertArrayHasKey('jsdelivr_url', $config);
        $this->assertEquals('Test App', $config['appName']);
        $this->assertEquals('http://localhost', $config['baseUrl']);
        $this->assertEquals(3, $config['jump_delay']);
        $this->assertTrue($config['enable_kill']);
        $this->assertTrue($config['enable_change_email']);
        $this->assertTrue($config['enable_r2_client_download']);
        $this->assertEquals('https://cdn.jsdelivr.net', $config['jsdelivr_url']);
    }
}
