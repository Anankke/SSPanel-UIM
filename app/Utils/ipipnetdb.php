<?php
namespace App\Utils;
/*
    全球 IPv4 地址归属地数据库(IPIP.NET 版)
    高春辉(pAUL gAO) <gaochunhui@gmail.com>
*/
class ipipnetdb
{
    private static $ip     = NULL;

    private static $fp     = NULL;
    private static $offset = NULL;
    private static $index  = NULL;

    public static function find($ip)
    {
        if (empty($ip) === TRUE)
        {
            return 'N/A';
        }

        $nip   = gethostbyname($ip);
        $ipdot = explode('.', $nip);

        if ($ipdot[0] < 0 || $ipdot[0] > 255 || count($ipdot) !== 4)
        {
            return 'N/A';
        }

        if (self::$fp === NULL)
        {
            self::init();
        }

        $nip2 = pack('N', ip2long($nip));

        $tmp_offset = ((int)$ipdot[0] * 256 + (int)$ipdot[1]) * 4;
        $start      = unpack('Vlen', self::$index[$tmp_offset] . self::$index[$tmp_offset + 1] . self::$index[$tmp_offset + 2] . self::$index[$tmp_offset + 3]);

        $index_offset = $index_length = NULL;
        $max_comp_len = self::$offset['len'] - 262144 - 4;
        for ($start = $start['len'] * 9 + 262144; $start < $max_comp_len; $start += 9)
        {
            if (self::$index{$start} . self::$index{$start + 1} . self::$index{$start + 2} . self::$index{$start + 3} >= $nip2)
            {
                $index_offset = unpack('Vlen', self::$index{$start + 4} . self::$index{$start + 5} . self::$index{$start + 6} . "\x0");
                $index_length = unpack('nlen', self::$index{$start + 7} . self::$index{$start + 8});

                break;
            }
        }

        if ($index_offset === NULL)
        {
            return 'N/A';
        }

        fseek(self::$fp, self::$offset['len'] + $index_offset['len'] - 262144);

        return explode("\t", fread(self::$fp, $index_length['len']));
    }

    private static function init()
    {
        if (self::$fp === NULL)
        {
            self::$ip = new self();

            self::$fp = fopen(BASE_PATH."/storage/ipipnetdb.datx", 'rb');
            if (self::$fp === FALSE)
            {
                return 'N/A';
            }

            self::$offset = unpack('Nlen', fread(self::$fp, 4));
            if (self::$offset['len'] < 4)
            {
                return 'N/A';
            }

            self::$index = fread(self::$fp, self::$offset['len'] - 4);
        }
    }

    public function __destruct()
    {
        if (self::$fp !== NULL)
        {
            fclose(self::$fp);

            self::$fp = NULL;
        }
    }
}

?>
