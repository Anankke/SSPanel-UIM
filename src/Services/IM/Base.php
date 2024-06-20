<?php

declare(strict_types=1);

namespace App\Services\IM;

abstract class Base
{
    abstract public function send($to, $msg): void;
}
