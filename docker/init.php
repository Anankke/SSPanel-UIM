<?php

declare(strict_types=1);

// Brings the database to the schema and settings of this image. Runs as the one-shot
// `init` service before web and cron start; exits non-zero when that cannot be done.

use App\Services\DB;

require '/app/vendor/autoload.php';
require '/app/config/.config.php';

const DB_WAIT_SECONDS = 60;

$deadline = time() + DB_WAIT_SECONDS;

while (true) {
    try {
        DB::init();
        break;
    } catch (Throwable $e) {
        if (time() >= $deadline) {
            fwrite(STDERR, '[sspanel] database not reachable after ' . DB_WAIT_SECONDS . ' s: '
                . $e->getMessage() . PHP_EOL);
            exit(1);
        }

        echo '[sspanel] waiting for database: ' . $e->getMessage() . PHP_EOL;
        sleep(2);
    }
}

// `Migration new` installs the base schema and only accepts a database without tables.
$commands = DB::schema()->getTables() === [] ? ['Migration new'] : [];
$commands[] = 'Migration latest';
$commands[] = 'Tool importSetting';

foreach ($commands as $command) {
    echo "[sspanel] php xcat {$command}" . PHP_EOL;
    passthru("php /app/xcat {$command}", $code);

    if ($code !== 0) {
        fwrite(STDERR, "[sspanel] php xcat {$command} failed with exit code {$code}" . PHP_EOL);
        exit($code);
    }
}
