<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

App\Utils\Cookie::set([
    'uid' => '1',
    'email' => 'user@example.com',
    'key' => 'key-value',
    'ip' => 'ip-value',
    'device' => 'device-value',
    'expire_in' => '9999999999',
], time() + 3600);

App\Utils\Cookie::setWithDomain(['scoped' => 'value'], time() + 3600, 'example.com');

echo 'ok';
