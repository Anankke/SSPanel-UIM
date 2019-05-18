<?php

namespace App\Utils;

use App\Models\User;
use App\Services\Config;

class QRcode
{
    public static function decode($url)
    {
        switch (Config::get('qrcode')) {
            case 'phpzbar':
                return QRcode::phpzbar_decode($url);
            case 'online':
                return QRcode::online_decode($url);
            case 'zxing_local':
                return QRcode::zxing_local_decode($url);
            default:
                return QRcode::zxing_decode($url);
        }
    }

    public static function online_decode($url)
    {
        $data = array();
        $data['fileurl'] = $url;
        $param = http_build_query($data, '', '&');

        $sock = new HTTPSocket;
        $sock->connect("api.qrserver.com", 80);
        $sock->set_method('GET');
        $sock->query('/v1/read-qr-code/', $param);

        $raw_text = $sock->fetch_body();
        $result_array = json_decode($raw_text, true);
        if (isset($result_array[0])) {
            return $result_array[0]['symbol'][0]['data'];
        }
        return null;
    }

    public static function phpzbar_decode($url)
    {
        $filepath = BASE_PATH . "/storage/" . time() . rand(1, 100) . ".png";
        $img = file_get_contents($url);
        file_put_contents($filepath, $img);

        /* Create new image object */
        $image = new \ZBarCodeImage($filepath);

        /* Create a barcode scanner */
        $scanner = new \ZBarCodeScanner();

        /* Scan the image */
        $barcode = $scanner->scan($image);

        unlink($filepath);

        return (isset($barcode[0]['data']) ? $barcode[0]['data'] : null);
    }

    public static function zxing_local_decode($url)
    {
        $filepath = BASE_PATH . "/storage/" . time() . rand(1, 100) . ".png";
        $img = file_get_contents($url);
        file_put_contents($filepath, $img);

        $qrcode = new \QrReader($filepath);
        $text = $qrcode->text(); //return decoded text from QR Code

        unlink($filepath);

        if ($text == null || $text == "") {
            return null;
        }

        return $text;
    }

    public static function zxing_decode($url)
    {
        $raw_text = file_get_contents("https://zxing.org/w/decode?u=" . urlencode($url));
        return Tools::get_middle_text($raw_text, "<tr><td>Raw text</td><td><pre>", "</pre></td></tr><tr><td>Raw bytes</td><td><pre>");
    }
}
