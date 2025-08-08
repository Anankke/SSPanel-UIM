<?php

/**
 * ClassHelper Utils tests using Pest
 */

use App\Utils\ClassHelper;

beforeEach(function () {
    $this->classHelper = new ClassHelper();
});

describe('ClassHelper::getClassesByNamespace', function () {
    it('returns array of classes for given namespace', function () {
        $namespace = 'App\\Utils';
        $classes = $this->classHelper->getClassesByNamespace($namespace);

        expect($classes)
            ->toBeArray()
            ->toContain('\App\Utils\ClassHelper');
    });
});

describe('ClassHelper::getClasses', function () {
    it('returns array of all available classes', function () {
        $classes = $this->classHelper->getClasses();

        expect($classes)
            ->toBeArray()
            ->toContain('\App\Utils\ClassHelper');
    });
});
