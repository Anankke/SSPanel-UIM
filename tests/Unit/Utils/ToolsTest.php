<?php

use App\Utils\Tools;

beforeEach(function () {
    $this->originalEnv = $_ENV;
});

afterEach(function () {
    $_ENV = $this->originalEnv;
});

describe('Tools::getIpLocation', function () {
    it('returns error message when maxmind service is not configured', function () {
        $_ENV['maxmind_license_key'] = '';
        
        $msg = Tools::getIpLocation('8.8.8.8');
        
        expect($msg)
            ->toBeString()
            ->toBe('GeoIP2 service not configured');
    });
});

describe('Tools::autoBytes', function () {
    it('converts bytes to human readable format', function () {
        $size = 1024;
        $bytes = Tools::autoBytes($size);
        
        expect($bytes)
            ->toBeString()
            ->toBe('1KB');
    });

    it('returns 0B for negative values', function () {
        $size = -1024;
        $bytes = Tools::autoBytes($size);
        
        expect($bytes)
            ->toBeString()
            ->toBe('0B');
    });
});

describe('Tools::autoBytesR', function () {
    it('converts human readable format to bytes', function () {
        expect(Tools::autoBytesR('1KB'))->toBe(1024)
            ->and(Tools::autoBytesR('1B'))->toBe(1);
    });

    it('returns -1 for invalid formats', function () {
        expect(Tools::autoBytesR('Highly illogical.'))->toBe(-1)
            ->and(Tools::autoBytesR('42069'))->toBe(-1)
            ->and(Tools::autoBytesR('1000EB'))->toBe(-1);
    });
});

describe('Tools::autoMbps', function () {
    it('converts bandwidth to human readable format', function () {
        expect(Tools::autoMbps(1))->toBe('1Mbps');
    });

    it('returns 0Bps for negative values', function () {
        expect(Tools::autoMbps(-1))->toBe('0Bps');
    });

    it('returns infinity symbol for very large values', function () {
        expect(Tools::autoMbps(1000000001))->toBe('∞');
    });
});

describe('Tools::mbToB', function () {
    it('converts megabytes to bytes', function () {
        $traffic = 1;
        $mb = 1048576;
        $result = Tools::mbToB($traffic);
        
        expect($result)
            ->toBeInt()
            ->toBe($traffic * $mb);
    });

    it('returns 0 for negative values', function () {
        expect(Tools::mbToB(-1))->toBe(0);
    });
});

describe('Tools::gbToB', function () {
    it('converts gigabytes to bytes', function () {
        $traffic = 1;
        $gb = 1048576 * 1024;
        $result = Tools::gbToB($traffic);
        
        expect($result)
            ->toBeInt()
            ->toBe($traffic * $gb);
    });

    it('returns 0 for negative values', function () {
        expect(Tools::gbToB(-1))->toBe(0);
    });
});

describe('Tools::bToGB', function () {
    it('converts bytes to gigabytes', function () {
        $traffic = 1048576 * 1024; // 1 GB in bytes
        $gb = 1048576 * 1024;
        $result = Tools::bToGB($traffic);
        
        expect($result)
            ->toBeFloat()
            ->toBe(1.0);
    });

    it('returns 0 for negative values', function () {
        expect(Tools::bToGB(-1))->toBe(0.0);
    });
});

describe('Tools::bToMB', function () {
    it('converts bytes to megabytes', function () {
        $traffic = 1048576; // 1 MB in bytes
        $mb = 1048576;
        $result = Tools::bToMB($traffic);
        
        expect($result)
            ->toBeFloat()
            ->toBe(1.0);
    });

    it('returns 0 for negative values', function () {
        expect(Tools::bToMB(-1))->toBe(0.0);
    });
});

describe('Tools::genSubToken', function () {
    it('generates token with specified length', function () {
        $_ENV['sub_token_len'] = 10;
        
        $token = Tools::genSubToken();
        
        expect(strlen($token))->toBe(10);
    });

    it('uses default length when ENV is 0 or negative', function () {
        $_ENV['sub_token_len'] = 0;
        expect(strlen(Tools::genSubToken()))->toBe(8);
        
        $_ENV['sub_token_len'] = -5;
        expect(strlen(Tools::genSubToken()))->toBe(8);
    });
});

describe('Tools::genRandomChar', function () {
    it('generates random string with default length', function () {
        $randomString = Tools::genRandomChar();
        
        expect($randomString)
            ->toBeString()
            ->and(strlen($randomString))->toBe(8);
    });

    it('generates random string with specified length', function () {
        $length = 10;
        $randomString = Tools::genRandomChar($length);
        
        expect($randomString)
            ->toBeString()
            ->and(strlen($randomString))->toBe($length);
    });

    it('enforces minimum length of 2', function () {
        $randomString = Tools::genRandomChar(1);
        
        expect($randomString)
            ->toBeString()
            ->and(strlen($randomString))->toBe(2);
    });
});

describe('Tools::genSs2022UserPk', function () {
    it('generates correct private key for AES-128', function () {
        $passwd = 'password';
        $method = '2022-blake3-aes-128-gcm';
        
        $pk = Tools::genSs2022UserPk($passwd, $method);
        
        expect($pk)
            ->toBeString()
            ->toBe('YzAwNjdkNGFmNGU4N2YwMA==');
    });

    it('generates correct private key for AES-256', function () {
        $passwd = 'password';
        $method = '2022-blake3-aes-256-gcm';
        
        $pk = Tools::genSs2022UserPk($passwd, $method);
        
        expect($pk)
            ->toBeString()
            ->toBe('YzAwNjdkNGFmNGU4N2YwMGRiYWM2M2I2MTU2ODI4MjM=');
    });

    it('returns false for invalid method', function () {
        $pk = Tools::genSs2022UserPk('password', 'bomb_three_gorges_dam');
        
        expect($pk)->toBeFalse();
    });
});

describe('Tools::toDateTime', function () {
    it('converts timestamp to datetime string', function () {
        date_default_timezone_set('ROC'); // Use Asia/Shanghai or PRC will cause this test to fail
        
        $time = 612907200; // 1989-06-04 04:00:00 UTC+8
        $result = Tools::toDateTime($time);
        expect($result)
            ->toBeString()
            ->toBe('1989-06-04 04:00:00');
        
        $time = -1830412800; // 1912-01-01 00:00:00 UTC+8
        $result = Tools::toDateTime($time);
        expect($result)
            ->toBeString()
            ->toBe('1912-01-01 00:00:00');
    });
});

describe('Tools::getDir', function () {
    it('returns directory contents', function () {
        $dir1 = 'tests/testDir';
        
        $result1 = Tools::getDir($dir1);
        
        // Just check that it returns an array (directory structure may vary)
        expect($result1)->toBeArray();
    });

    it('returns .gitkeep for empty directory', function () {
        $dir2 = 'tests/testDir/emptyDir';
        
        $result2 = Tools::getDir($dir2);
        
        expect($result2)->toMatchArray(['.gitkeep']);
    });
});

describe('Tools::isParamValidate', function () {
    it('validates encryption parameters correctly', function () {
        expect(Tools::isParamValidate('default', 'aes-128-gcm'))->toBeTrue()
            ->and(Tools::isParamValidate('default', 'rc4-md5'))->toBeFalse();
    });
});

describe('Tools::getSsMethod', function () {
    it('returns ss_obfs methods', function () {
        $expected = [
            'simple_obfs_http',
            'simple_obfs_http_compatible',
            'simple_obfs_tls',
            'simple_obfs_tls_compatible',
        ];
        
        $result = Tools::getSsMethod('ss_obfs');
        
        expect($result)->toBe($expected);
    });

    it('returns default methods for default/random/empty string', function () {
        $expected = [
            'aes-128-gcm',
            'aes-192-gcm',
            'aes-256-gcm',
            'chacha20-ietf-poly1305',
            'xchacha20-ietf-poly1305',
        ];
        
        expect(Tools::getSsMethod('default'))->toBe($expected)
            ->and(Tools::getSsMethod('randomString'))->toBe($expected)
            ->and(Tools::getSsMethod())->toBe($expected);
    });
});

describe('Tools validation methods', function () {
    test('isEmail validates email addresses', function () {
        expect(Tools::isEmail('test@example.com'))->toBeTrue()
            ->and(Tools::isEmail('test@example'))->toBeFalse();
    });

    test('isIPv4 validates IPv4 addresses', function () {
        expect(Tools::isIPv4('192.168.0.1'))->toBeTrue()
            ->and(Tools::isIPv4('2001:0db8:85a3:0000:0000:8a2e:0370:7334'))->toBeFalse()
            ->and(Tools::isIPv4('UwU'))->toBeFalse();
    });

    test('isIPv6 validates IPv6 addresses', function () {
        expect(Tools::isIPv6('2001:0db8:85a3:0000:0000:8a2e:0370:7334'))->toBeTrue()
            ->and(Tools::isIPv6('192.168.0.1'))->toBeFalse()
            ->and(Tools::isIPv6('hmm'))->toBeFalse();
    });

    test('isInt validates integers', function () {
        expect(Tools::isInt(123))->toBeTrue()
            ->and(Tools::isInt('abc'))->toBeFalse();
    });

    test('isJson validates JSON objects', function () {
        expect(Tools::isJson('{}'))->toBeTrue()
            ->and(Tools::isJson('[]'))->toBeFalse()
            ->and(Tools::isJson('what the'))->toBeFalse();
    });
});