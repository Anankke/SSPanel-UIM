<?php
namespace GuzzleHttp\Tests\Psr7;

require __DIR__ . '/../vendor/autoload.php';

class HasToString
{
    public function __toString() {
        return 'foo';
    }
}
