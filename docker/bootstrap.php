<?php

declare(strict_types=1);

// Creates the site configuration files on first start and refuses to continue when
// the settings every request depends on are unusable. Existing files are never touched.

$key = bin2hex(random_bytes(32));
$muKey = bin2hex(random_bytes(32));

$files = [];

$files['/data/sspanel/.config.php'] = <<<PHP
<?php

// SSPanel-UIM settings for this site, created on first start of the Docker image.
// Database, Redis and site address come from the SSPANEL_* environment variables
// (see /app/docker/config.php); every other setting has the default of the running
// image unless you set it at the end of this file.

require '/app/docker/config.php';

// Generated on first start; SSPANEL_KEY / SSPANEL_MU_KEY take precedence when set.
\$_ENV['key'] = sspanel_env('SSPANEL_KEY', '{$key}');
\$_ENV['muKey'] = sspanel_env('SSPANEL_MU_KEY', '{$muKey}');

// Your settings, e.g.:
// \$_ENV['appName'] = 'My Panel';
// \$_ENV['timeZone'] = 'Asia/Shanghai';

PHP;

$files['/data/sspanel/appprofile.php'] = <<<'PHP'
<?php

// Subscription templates. The defaults ship with the image and follow its upgrades;
// override a template by reassigning it below, e.g. $_ENV['Clash_Config'] = [...];

require '/app/config/appprofile.example.php';

PHP;

foreach ($files as $path => $content) {
    if (! file_exists($path)) {
        file_put_contents($path, $content);
        echo "[sspanel] created {$path}" . PHP_EOL;
    }
}

require '/app/config/.config.php';

$errors = [];

foreach (['key' => 'SSPANEL_KEY', 'muKey' => 'SSPANEL_MU_KEY'] as $setting => $variable) {
    if ($_ENV[$setting] === '' || $_ENV[$setting] === 'ChangeMe') {
        $errors[] = "{$setting} is not set ({$variable})";
    }
}

$baseUrl = $_ENV['baseUrl'];

if (! str_starts_with($baseUrl, 'https://') || str_ends_with($baseUrl, '/') || $baseUrl === 'https://example.com') {
    $errors[] = "baseUrl must be the site's https:// address without a trailing slash (SSPANEL_BASE_URL), got '{$baseUrl}'";
}

if ($errors !== []) {
    foreach ($errors as $error) {
        fwrite(STDERR, "[sspanel] configuration error: {$error}" . PHP_EOL);
    }

    exit(1);
}
