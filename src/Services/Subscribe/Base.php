<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

abstract class Base
{
    abstract public function getContent($user): string;
}
