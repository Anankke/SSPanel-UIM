<?php

declare(strict_types=1);

namespace App\Utils;

use function is_null;

final class ClassHelper
{
    private static mixed $composer = null;
    private static array $classes = [];

    public function __construct()
    {
        self::$composer = null;
        self::$classes = [];

        self::$composer = require __DIR__ . '/../../vendor/autoload.php';

        if (is_null(self::$composer) === false) {
            self::$classes = array_keys(self::$composer->getClassMap());
        }
    }

    public function getClasses(): array
    {
        $allClasses = [];

        if (is_null(self::$classes) === false) {
            foreach (self::$classes as $class) {
                $allClasses[] = '\\' . $class;
            }
        }

        return $allClasses;
    }

    public function getClassesByNamespace($namespace): array
    {
        if (! str_starts_with($namespace, '\\')) {
            $namespace = '\\' . $namespace;
        }

        $termUpper = strtoupper($namespace);
        return array_filter($this->getClasses(), static function ($class) use ($termUpper) {
            $className = strtoupper($class);
            if (
                str_starts_with($className, $termUpper) &&
                ! str_contains($className, strtoupper('Abstract')) &&
                ! str_contains($className, strtoupper('Interface'))
            ) {
                return $class;
            }
            return false;
        });
    }
}
