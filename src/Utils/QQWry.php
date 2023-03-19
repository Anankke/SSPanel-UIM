<?php

declare(strict_types=1);

namespace App\Utils;

use function chr;
use function ord;

final class QQWry
{
    private $fp;

    private mixed $firstip;

    private mixed $lastip;

    private int|float $totalip;

    public function __construct()
    {
        $filename = BASE_PATH . '/storage/qqwry.dat';
        $this->fp = fopen($filename, 'rb');

        if ($this->fp !== false) {
            $this->firstip = $this->getlong();
            $this->lastip = $this->getlong();
            $this->totalip = ($this->lastip - $this->firstip) / 7;
            register_shutdown_function([&$this, '__destruct']);
        }
    }

    public function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp = 0;
    }

    public function getlocation($ip): ?array
    {
        if (! $this->fp) {
            return null;
        }

        $location = [];
        $location['ip'] = gethostbyname($ip);
        $ip = $this->packip($location['ip']);
        $l = 0;
        $u = $this->totalip;
        $findip = $this->lastip;

        while ($l <= $u) {
            $i = floor(($l + $u) / 2);
            fseek($this->fp, (int) ($this->firstip + $i * 7));
            $beginip = strrev(fread($this->fp, 4));

            if ($ip < $beginip) {
                $u = $i - 1;
            } else {
                fseek($this->fp, $this->getlong3());
                $endip = strrev(fread($this->fp, 4));

                if ($ip > $endip) {
                    $l = $i + 1;
                } else {
                    $findip = (int) ($this->firstip + $i * 7);
                    break;
                }
            }
        }

        fseek($this->fp, $findip);
        $location['beginip'] = long2ip($this->getlong());
        $offset = $this->getlong3();
        fseek($this->fp, $offset);
        $location['endip'] = long2ip($this->getlong());
        $byte = fread($this->fp, 1);

        switch (ord($byte)) {
            case 1:
                $countryOffset = $this->getlong3();
                fseek($this->fp, $countryOffset);
                $byte = fread($this->fp, 1);

                if (ord($byte) === 2) {
                    fseek($this->fp, $this->getlong3());
                    $location['country'] = $this->getstring();
                    fseek($this->fp, $countryOffset + 4);
                } else {
                    $location['country'] = $this->getstring($byte);
                }
                break;
            case 2:
                fseek($this->fp, $this->getlong3());
                $location['country'] = $this->getstring();
                fseek($this->fp, $offset + 8);
                break;
            default:
                $location['country'] = $this->getstring($byte);
                break;
        }

        $location['area'] = $this->getarea();

        if ($location['country'] === ' CZ88.NET') {
            $location['country'] = '未知';
        }

        if ($location['area'] === ' CZ88.NET') {
            $location['area'] = '';
        }

        return $location;
    }

    private function getlong()
    {
        $result = unpack('Vlong', fread($this->fp, 4));
        return $result['long'];
    }

    private function getlong3()
    {
        $result = unpack('Vlong', fread($this->fp, 3) . chr(0));
        return $result['long'];
    }

    private function packip($ip): string
    {
        return pack('N', (int) ip2long($ip));
    }

    private function getstring($data = '')
    {
        $char = fread($this->fp, 1);

        while (ord($char) > 0) {
            $data .= $char;
            $char = fread($this->fp, 1);
        }

        return $data;
    }

    private function getarea()
    {
        $byte = fread($this->fp, 1);

        switch (ord($byte)) {
            case 0:
                $area = '';
                break;
            case 1:
            case 2:
                fseek($this->fp, $this->getlong3());
                $area = $this->getstring();
                break;
            default:
                $area = $this->getstring($byte);
                break;
        }

        return $area;
    }
}
