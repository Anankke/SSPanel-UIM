<?php
declare(strict_types=1);

namespace App\Utils;

use Zxing\QrReader;

class QRcode
{
    /**
     * Decode QR code
     *
     * @param   string      $source     QR code source url/path
     *
     * @return  string|null             Decoded string, null for empty
     */
    public static function decode(string $source)
    {
        $img = file_get_contents($source);
        $qrcode = new QrReader($img, QrReader::SOURCE_TYPE_BLOB);
        $text = $qrcode->text();
        if ($text == false || $text == '') {
            return null;
        }
        return $text;
    }
}
