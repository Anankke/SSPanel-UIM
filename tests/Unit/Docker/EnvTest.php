<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../docker/env.php';

beforeEach(function () {
    putenv('SSPANEL_TEST_VALUE');
    putenv('SSPANEL_TEST_VALUE_FILE');
});

afterEach(function () {
    putenv('SSPANEL_TEST_VALUE');
    putenv('SSPANEL_TEST_VALUE_FILE');
});

it('returns the default when the variable is not set', function () {
    expect(sspanel_env('SSPANEL_TEST_VALUE', 'fallback'))->toBe('fallback');
    expect(sspanel_env('SSPANEL_TEST_VALUE'))->toBeNull();
});

it('returns the variable when it is set, including an empty value', function () {
    putenv('SSPANEL_TEST_VALUE=from-env');
    expect(sspanel_env('SSPANEL_TEST_VALUE', 'fallback'))->toBe('from-env');

    putenv('SSPANEL_TEST_VALUE=');
    expect(sspanel_env('SSPANEL_TEST_VALUE', 'fallback'))->toBe('');
});

it('reads NAME_FILE before NAME and strips the trailing newline', function () {
    $file = tempnam(sys_get_temp_dir(), 'sspanel');
    file_put_contents($file, "from-file\n");
    putenv('SSPANEL_TEST_VALUE=from-env');
    putenv("SSPANEL_TEST_VALUE_FILE={$file}");

    expect(sspanel_env('SSPANEL_TEST_VALUE', 'fallback'))->toBe('from-file');

    unlink($file);
});

it('fails when NAME_FILE cannot be read', function () {
    putenv('SSPANEL_TEST_VALUE_FILE=/nonexistent/sspanel-secret');

    sspanel_env('SSPANEL_TEST_VALUE');
})->throws(RuntimeException::class, 'SSPANEL_TEST_VALUE_FILE');
