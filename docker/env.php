<?php

declare(strict_types=1);

/**
 * Read a setting from the container environment.
 *
 * NAME_FILE takes precedence over NAME and names a file whose content (without the
 * trailing newline) is the value, the convention used for Docker secrets.
 */
function sspanel_env(string $name, ?string $default = null): ?string
{
    $file = getenv($name . '_FILE');

    if ($file !== false && $file !== '') {
        if (! is_file($file) || ! is_readable($file)) {
            throw new RuntimeException("{$name}_FILE: cannot read {$file}");
        }

        return rtrim(file_get_contents($file), "\r\n");
    }

    $value = getenv($name);

    return $value === false ? $default : $value;
}
