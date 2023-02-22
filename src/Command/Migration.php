<?php

declare(strict_types=1);

namespace App\Command;

use App\Interfaces\MigrationInterface;
use App\Models\Setting;
use App\Services\DB;
use function count;
use function explode;
use function is_numeric;
use function krsort;
use function ksort;
use function scandir;
use function substr;
use const PHP_INT_MAX;
use const SCANDIR_SORT_NONE;

final class Migration extends Command
{
    public $description = <<< END
├─=: php xcat Migration [版本]
│ ├─ <version>               - 迁移至指定版本（前进/退回）
│ ├─ latest                  - 迁移至最新版本
│ ├─ new                     - 导入全新数据库至最新版本
END;

    public function boot(): void
    {
        $reverse = false;
        // (min_version, max_version]
        $min_version = 0;
        $max_version = 0;
        
        $target = $this->argv[2] ?? 0;

        if ($target === 'new') {
            $current = 0;
        } else {
            $current = Setting::obtain('db_version');
        }

        if ($target === 'latest') {
            $min_version = $current;
            $max_version = PHP_INT_MAX;
        } elseif ($target === 'new') {
            $tables = DB::select('SHOW TABLES');
            if ($tables === []) {
                $min_version = 0;
                $max_version = PHP_INT_MAX;
            } else {
                echo "Database is not empty, do not use 'new' version.\n";
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
            echo "Illegal version argument.\n";
            return;
        }

        echo "Current database version {$current}.\n";

        $queue = [];
        $files = scandir(BASE_PATH . '/db/migrations/', SCANDIR_SORT_NONE);
        if ($files) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || substr($file, -4) !== '.php') {
                    continue;
                }
                $version = (int) (explode('-', $file, 1)[0] ?? 0);
                echo "Found migration version {$version}.\n";
                if ($version <= $min_version || $version > $max_version) {
                    echo "Skip migration version {$version}.\n";
                    continue;
                }
                $object = require BASE_PATH . '/db/migrations/' . $file;
                if ($object instanceof MigrationInterface) {
                    $queue[$version] = $object;
                }
            }
        }

        if ($reverse) {
            krsort($queue);
            foreach ($queue as $version => $object) {
                echo "Reverse to {$version}\n";
                $object->down();
                if ($version < $current) {
                    $current = $version;
                }
            }
        } else {
            ksort($queue);
            foreach ($queue as $version => $object) {
                echo "Forward to {$version}\n";
                $object->up();
                if ($version > $current) {
                    $current = $version;
                }
            }
        }
        if ($target === 'new') {
            $sql = 'INSERT INTO `config` (`item`, `value`, `type`, `default`) VALUES("db_version", ?, "int", "20230201000")';
        } else {
            $sql = 'UPDATE `config` SET `value` = ? WHERE `item` = "db_version"';
        }
        DB::insert($sql, [$current]);

        $count = count($queue);
        echo "Migration complete. {$count} item(s) processed.\n";
    }
}
