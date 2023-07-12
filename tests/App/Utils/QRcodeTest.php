<?php

declare(strict_types=1);

namespace App\Utils;

use App\Utils\QRcode;
use PHPUnit\Framework\TestCase;

class QRcodeTest extends TestCase
{
    /**
     * @covers App\Utils\QRcode::decode
     */
    public function testDecodeWithValidData(): void
    {
        $qrCodeImg = __DIR__ . '/qrcode.png';
        $expectedText = 'https://www.example.com';

        $result = QRcode::decode($qrCodeImg);

        $this->assertSame($expectedText, $result);
    }

    /**
     * @covers App\Utils\QRcode::decode
     */
    public function testDecodeWithInvalidData(): void
    {
        $invalidImg = __DIR__ . '/invalid.png';

        $result = QRcode::decode($invalidImg);

        $this->assertNull($result);
    }
}
