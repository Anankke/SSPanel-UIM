<?php

declare(strict_types=1);

namespace App\Utils;

final class ClassHelper
{
    private static $composer = null;
    private static $classes = [];

    public function __construct()
    {
        self::$composer = null;
        self::$classes = [];

        self::$composer = require __DIR__ . '/../../vendor/autoload.php';

        if (is_null(self::$composer) === false) {
            self::$classes = array_keys(self::$composer->getClassMap());
        }
    }

    public function getClasses()
    {
        $allClasses = [];

        if (is_null(self::$classes) === false) {
            foreach (self::$classes as $class) {
                $allClasses[] = '\\' . $class;
            }
        }

        return $allClasses;
    }

    public function getClassesByNamespace($namespace)
    {
        if (strpos($namespace, '\\') !== 0) {
            $namespace = '\\' . $namespace;
        }

        $termUpper = strtoupper($namespace);
        return array_filter($this->getClasses(), static function ($class) use ($termUpper) {
            $className = strtoupper($class);
            if (
                strpos($className, $termUpper) === 0 and
                strpos($className, strtoupper('Abstract')) === false and
                strpos($className, strtoupper('Interface')) === false
            ) {
                return $class;
            }
            return false;
        });
    }
}
