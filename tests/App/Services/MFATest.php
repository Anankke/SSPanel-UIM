<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use PHPUnit\Framework\TestCase;
use function strlen;

class MFATest extends TestCase
{
    /**
     * @covers App\Services\MFA::generateGaToken
     * @throws Exception
     */
    public function testGenerateGaToken()
    {
        $token = MFA::generateGaToken();
        $this->assertIsString($token);
        $this->assertGreaterThan(0, strlen($token));
        $this->assertEquals(16, strlen($token));
    }

    /**
     * @covers App\Services\MFA::verifyGa
     */
    public function testVerifyGa()
    {
        $user = (object) ['ga_token' => 'SECRET'];
        $this->assertFalse(MFA::verifyGa($user, '000000'));
        $this->assertFalse(MFA::verifyGa($user, 'test'));
        $this->assertFalse(MFA::verifyGa($user, '0'));
    }

    /**
     * @covers App\Services\MFA::getGaUrl
     */
    public function testGetGaUrl()
    {
        $_ENV['appName'] = 'Test';
        $user = (object) ['email' => 'test@example.com', 'ga_token' => 'SECRET'];
        $url = MFA::getGaUrl($user);
        $this->assertStringContainsString('otpauth://totp/', $url);
        $this->assertStringContainsString(rawurlencode('Test' . ' (' . $user->email . ')'), $url);
        $this->assertStringContainsString('secret=' . $user->ga_token, $url);
    }
}
