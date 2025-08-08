<?php

/**
 * Cookie Utils tests using Pest
 */

use App\Utils\Cookie;

describe('Cookie::set', function () {
    it('accepts correct parameters without throwing error', function () {
        $data = ['testKey' => 'testValue'];
        $time = time() + 3600;

        // In CLI environment, we can't test setcookie directly
        // Instead, we verify the method accepts correct parameters
        // If this doesn't throw an error, the method signature is correct
        expect(fn() => Cookie::set($data, $time))->not->toThrow(\Exception::class);
    });
});

describe('Cookie::setWithDomain', function () {
    it('accepts correct parameters with domain without throwing error', function () {
        $data = ['testKey' => 'testValue'];
        $time = time() + 3600;
        $domain = 'localhost';

        // In CLI environment, we can't test setcookie directly
        // Instead, we verify the method accepts correct parameters
        expect(fn() => Cookie::setWithDomain($data, $time, $domain))->not->toThrow(\Exception::class);
    });
});

describe('Cookie::get', function () {
    it('retrieves cookie value from $_COOKIE superglobal', function () {
        // Test get method by directly setting $_COOKIE
        $_COOKIE['testKey'] = 'testValue';
        
        expect(Cookie::get('testKey'))->toBe('testValue');
        
        // Clean up
        unset($_COOKIE['testKey']);
    });

    it('returns empty string for non-existent key', function () {
        expect(Cookie::get('nonExistentKey'))->toBe('');
    });
});
