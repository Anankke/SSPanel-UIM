<?php

declare(strict_types=1);

namespace App\Command;

use App\Interfaces\MigrationInterface;
use App\Models\Config;
use App\Services\DB;
use function count;
use function explode;
use function is_numeric;
use function krsort;
use function ksort;
use function scandir;
use const PHP_EOL;
use const PHP_INT_MAX;
use const SCANDIR_SORT_NONE;

final class Migration extends Command
{
    public string $description = <<< END
├─=: php xcat Migration [版本]
│ ├─ <version> - 迁移至指定版本（前进/退回）
│ ├─ latest    - 迁移至最新版本
│ ├─ new       - 导入全新数据库至最新版本
END;

    public function boot(): void
    {
        $reverse = false;
        $current = 0;
        $latest = 0;
        $min_version = 0;
        $max_version = 0;
        $target = $this->argv[2] ?? 0;

        if ($target !== 'new') {
            $current = Config::obtain('db_version');
        }

        if ($target === 'latest') {
            $min_version = $current;
            $max_version = PHP_INT_MAX;
        } elseif ($target === 'new') {
            $tables = DB::select('SHOW TABLES');

            if ($tables === []) {
                $max_version = PHP_INT_MAX;
            } else {
                echo 'Database is not empty, do not use "new" as version.' . PHP_EOL;

                return;
            }
        } elseif (is_numeric($target)) {
            $target = (int) $target;

            if ($target < $current) {
                $reverse = true;
                $min_version = $target;
                $max_version = $current;
            } else {
                $min_version = $current;
                $max_version = $target;
            }
        } else {
            echo 'Illegal version argument.' . PHP_EOL;

            return;
        }

        echo 'Current database version ' . $current . PHP_EOL . PHP_EOL;

        $queue = [];
        $files = scandir(BASE_PATH . '/db/migrations/', SCANDIR_SORT_NONE);

        if ($files) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || ! str_ends_with($file, '.php')) {
                    continue;
                }

                $version = (int) explode('-', $file, 1)[0];
                echo 'Found migration version ' . $version;

                if ($version > $latest) {
                    $latest = $version;
                }

                if ($version <= $min_version ||
                    $version > $max_version ||
                    ($target === 'new' && $version !== 2023020100)
                ) {
                    echo '...skip' . PHP_EOL;
                    continue;
                }

                echo PHP_EOL;

                $object = require BASE_PATH . '/db/migrations/' . $file;

                if ($object instanceof MigrationInterface) {
                    $queue[$version] = $object;
                }
            }
        }

        echo PHP_EOL;
        echo 'Latest database version ' . $latest . PHP_EOL . PHP_EOL;

        if ($reverse) {
            krsort($queue);

            foreach ($queue as $version => $object) {
                echo 'Rollback to ' . $version . PHP_EOL;
                $current = $object->down();
            }
        } else {
            ksort($queue);

            foreach ($queue as $version => $object) {
                echo 'Forward to ' . $version . PHP_EOL;
                $current = $object->up();
            }
        }

        $sql = match ($target) {
            'new' => 'INSERT INTO `config` (`item`, `value`, `type`, `default`)
                        VALUES("db_version", ?, "int", "2023020100")',
            default => 'UPDATE `config` SET `value` = ? WHERE `item` = "db_version"'
        };

        $stat = DB::getPdo()->prepare($sql);

        if ($target === 'new') {
            $stat->execute([$latest]);
        } else {
            $stat->execute([$current]);
        }

        $count = count($queue);

        echo 'Migration completed. ' . $count . ' file(s) processed.' . PHP_EOL
            . 'Current database version: ' . $current . PHP_EOL;
    }
}
