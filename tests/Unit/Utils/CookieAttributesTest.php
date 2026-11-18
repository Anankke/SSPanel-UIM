<?php

declare(strict_types=1);

beforeEach(function () {
    $this->cookieServer = null;
    $this->cookieServerPipes = [];
    $this->cookieServerPort = 0;
});

afterEach(function () {
    if (is_resource($this->cookieServer)) {
        proc_terminate($this->cookieServer);
        proc_close($this->cookieServer);
    }

    foreach ($this->cookieServerPipes as $pipe) {
        if (is_resource($pipe)) {
            fclose($pipe);
        }
    }
});

function cookieTestServer(): array
{
    for ($attempt = 0; $attempt < 10; $attempt++) {
        $port = random_int(20000, 40000);
        $pipes = [];
        $process = proc_open(
            [PHP_BINARY, '-S', '127.0.0.1:' . $port, __DIR__ . '/../../Fixtures/cookie-router.php'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );

        if (! is_resource($process)) {
            continue;
        }

        for ($i = 0; $i < 60; $i++) {
            $socket = cookieTestConnect($port);

            if ($socket !== false) {
                fclose($socket);

                return [$process, $pipes, $port];
            }

            usleep(50000);
        }

        proc_terminate($process);
        proc_close($process);

        foreach ($pipes as $pipe) {
            if (is_resource($pipe)) {
                fclose($pipe);
            }
        }
    }

    throw new RuntimeException('could not start the PHP built-in server');
}

function cookieTestConnect(int $port)
{
    set_error_handler(static fn (): bool => true);

    try {
        return fsockopen('127.0.0.1', $port, $errno, $errstr, 0.2);
    } finally {
        restore_error_handler();
    }
}

function cookieTestSetCookies(int $port): array
{
    set_error_handler(static fn (): bool => true);

    try {
        $body = file_get_contents('http://127.0.0.1:' . $port . '/');
    } finally {
        restore_error_handler();
    }

    if ($body !== 'ok') {
        throw new RuntimeException('unexpected body: ' . $body);
    }

    return array_values(array_filter(
        $http_response_header ?? [],
        static fn (string $header): bool => stripos($header, 'Set-Cookie:') === 0
    ));
}

function cookieTestHeader(array $headers, string $name): string
{
    foreach ($headers as $header) {
        if (stripos($header, 'Set-Cookie: ' . $name . '=') === 0) {
            return $header;
        }
    }

    return '';
}

it('sets every cookie with SameSite=Lax, Secure and HttpOnly', function () {
    [$process, $pipes, $port] = cookieTestServer();
    $this->cookieServer = $process;
    $this->cookieServerPipes = $pipes;

    $headers = cookieTestSetCookies($port);

    expect($headers)->toHaveCount(7);

    foreach (['uid', 'email', 'key', 'ip', 'device', 'expire_in'] as $name) {
        $header = strtolower(cookieTestHeader($headers, $name));

        expect($header)->not->toBeEmpty()
            ->and($header)->toContain('set-cookie: ' . $name . '=')
            ->and($header)->toContain('samesite=lax')
            ->and($header)->toContain('secure')
            ->and($header)->toContain('httponly')
            ->and($header)->toContain('path=/')
            ->and($header)->toContain('expires=')
            ->and($header)->toContain('max-age=');
    }
});

it('keeps the domain attribute when one is given', function () {
    [$process, $pipes, $port] = cookieTestServer();
    $this->cookieServer = $process;
    $this->cookieServerPipes = $pipes;

    $header = strtolower(cookieTestHeader(cookieTestSetCookies($port), 'scoped'));

    expect($header)->not->toBeEmpty()
        ->and($header)->toContain('domain=example.com')
        ->and($header)->toContain('samesite=lax')
        ->and($header)->toContain('secure')
        ->and($header)->toContain('httponly')
        ->and($header)->toContain('path=/');
});
