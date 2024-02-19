<?php

declare(strict_types=1);

namespace App\Utils;

use PHPUnit\Framework\TestCase;
use App\Utils\ClassHelper;

final class ClassHelperTest extends TestCase
{
    private ClassHelper $classHelper;

    protected function setUp(): void
    {
        $this->classHelper = new ClassHelper();
    }

    /**
     * @covers App\Utils\ClassHelper::getClassesByNamespace
     */
    public function testGetClassesByNamespace(): void
    {
        $namespace = 'App\\Utils';
        $classes = $this->classHelper->getClassesByNamespace($namespace);

        $this->assertIsArray($classes);
        $this->assertContains('\App\Utils\ClassHelper', $classes);
    }

    /**
     * @covers App\Utils\ClassHelper::getClasses
     */
    public function testGetClasses(): void
    {
        $classes = $this->classHelper->getClasses();

        $this->assertIsArray($classes);
        $this->assertContains('\App\Utils\ClassHelper', $classes);
    }
}
