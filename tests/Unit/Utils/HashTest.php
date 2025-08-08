<?php

use App\Utils\Hash;

beforeEach(function () {
    $this->originalEnv = $_ENV;
});

afterEach(function () {
    $_ENV = $this->originalEnv;
});

describe('Hash::cookieHash', function () {
    it('generates consistent hash for cookie authentication', function () {
        $_ENV['key'] = 'cookie_key';
        $passHash = 'password';
        $expire_in = 69420;
        
        $result = Hash::cookieHash($passHash, $expire_in);
        
        expect($result)
            ->toBeString()
            ->and(strlen($result))->toBe(45)
            ->and($result)->toBe('e91053c4a7d6cc7fa5eb900b1ad96df484483ceace12a');
    });
});

describe('Hash::ipHash', function () {
    it('generates consistent hash for IP validation', function () {
        $_ENV['key'] = 'cookie_key';
        $ip = '192.168.0.1';
        $uid = 69;
        $expire_in = 69420;
        
        $result = Hash::ipHash($ip, $uid, $expire_in);
        
        expect($result)
            ->toBeString()
            ->and(strlen($result))->toBe(45)
            ->and($result)->toBe('522b51095b778f9f107153f75be554be1f8a8f2c1f4b4');
    });
});

describe('Hash::deviceHash', function () {
    it('generates consistent hash for device validation', function () {
        $_ENV['key'] = 'cookie_key';
        $device = 'Firefox/119.0';
        $uid = 69;
        $expire_in = 69420;
        
        $result = Hash::deviceHash($device, $uid, $expire_in);
        
        expect($result)
            ->toBeString()
            ->and(strlen($result))->toBe(45)
            ->and($result)->toBe('1fd5a37cc8769c01a49f6eb9c167dc6ee6cc842913dba');
    });
});

describe('Hash password functions', function () {
    beforeEach(function () {
        $_ENV['salt'] = 'password_salt';
    });

    test('bcrypt method hashes and validates correctly', function () {
        $_ENV['pwdMethod'] = 'bcrypt';
        $password = 'password';
        
        $hashedPassword = Hash::passwordHash($password);
        
        expect(Hash::checkPassword($hashedPassword, $password))->toBeTrue()
            ->and(Hash::checkPassword($hashedPassword, 'wrong_password'))->toBeFalse();
    });

    test('argon2i method hashes and validates correctly', function () {
        $_ENV['pwdMethod'] = 'argon2i';
        $password = 'password';
        
        $hashedPassword = Hash::passwordHash($password);
        
        expect(Hash::checkPassword($hashedPassword, $password))->toBeTrue()
            ->and(Hash::checkPassword($hashedPassword, 'wrong_password'))->toBeFalse();
    });

    test('argon2id method hashes and validates correctly', function () {
        $_ENV['pwdMethod'] = 'argon2id';
        $password = 'password';
        
        $hashedPassword = Hash::passwordHash($password);
        
        expect(Hash::checkPassword($hashedPassword, $password))->toBeTrue()
            ->and(Hash::checkPassword($hashedPassword, 'wrong_password'))->toBeFalse();
    });

    test('sha256 method hashes and validates correctly', function () {
        $_ENV['pwdMethod'] = 'sha256';
        $password = 'password';
        
        $hashedPassword = Hash::passwordHash($password);
        
        expect(Hash::checkPassword($hashedPassword, $password))->toBeTrue()
            ->and(Hash::checkPassword($hashedPassword, 'wrong_password'))->toBeFalse();
    });

    test('sha3 method hashes and validates correctly', function () {
        $_ENV['pwdMethod'] = 'sha3';
        $password = 'password';
        
        $hashedPassword = Hash::passwordHash($password);
        
        expect(Hash::checkPassword($hashedPassword, $password))->toBeTrue()
            ->and(Hash::checkPassword($hashedPassword, 'wrong_password'))->toBeFalse();
    });
});
