<?php

declare(strict_types=1);

namespace App\Utils;

use Exception;
use Zxing\QrReader;
use function file_get_contents;

final class QRcode
{
    /**
     * Decode QR code
     *
     * @param   string      $source     QR code source url/path
     *
     * @return  string|null             Decoded string, null for empty
     */
    public static function decode(string $source): ?string
    {
        if (! file_exists($source)) {
            return null;
        }

        $img = file_get_contents($source);

        try {
            $qrcode = new QrReader($img, QrReader::SOURCE_TYPE_BLOB);
            $text = $qrcode->text();
        } catch (Exception $e) {
            $text = '';
        }

        if ($text === '') {
            return null;
        }

        return $text;
    }
}
