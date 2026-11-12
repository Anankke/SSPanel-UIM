<?php

declare(strict_types=1);

namespace App\Command;

use App\Interfaces\MigrationInterface;
use App\Models\Config;
use App\Services\DB;
use Illuminate\Database\QueryException;
use LogicException;
use Throwable;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function basename;
use function count;
use function ctype_digit;
use function explode;
use function glob;
use function is_int;
use function ksort;
use function max;
use function min;
use function rsort;
use function str_replace;
use function trim;
use const PHP_EOL;

final class Migration extends Command
{
    public const EXIT_OK = 0;
    public const EXIT_FAILED = 1;
    public const EXIT_USAGE = 2;
    public const EXIT_LOCKED = 3;

    public string $description = <<< END
├─=: php xcat Migration [版本]
│ ├─ <version> - 迁移至指定版本（前进/退回）
│ ├─ latest    - 迁移至最新版本
│ ├─ new       - 导入全新数据库（仅限空库，库非空时退出码 2）
│ ├─ 退出码：0 完成；1 某个迁移失败，db_version 停在最后一个成功的迁移；2 参数或库状态无法识别，库未改动；3 保留（另一个迁移进程正在运行），当前版本不会返回
END;

    public function boot(): void
    {
        $target = $this->parseTarget();
        $files = $this->scanFiles();

        if (is_int($target) && ! isset($files[$target])) {
            $this->stop('Unknown migration version ' . $target . '.', self::EXIT_USAGE);
        }

        if ($target === 'new') {
            $this->install($files);

            return;
        }

        $current = $this->currentVersion();
        $limit = $target === 'latest' ? max(array_keys($files)) : $target;
        $plan = $this->plan($files, $current, $limit);

        echo 'Current database version ' . $current . PHP_EOL . PHP_EOL;

        $current = $this->apply($files, $plan, $limit < $current ? 'down' : 'up', $current);

        $this->finish(count($plan), $current);
    }

    private function stop(string $message, int $code): never
    {
        echo $message . PHP_EOL;

        exit($code);
    }

    /**
     * @return int|string a version, "latest" or "new"
     */
    private function parseTarget(): int|string
    {
        $target = $this->argv[2] ?? null;

        if ($target === null) {
            $this->stop(trim($this->description), self::EXIT_USAGE);
        }

        if ($target === 'latest' || $target === 'new') {
            return $target;
        }

        if (ctype_digit($target)) {
            return (int) $target;
        }

        $this->stop('Illegal version argument.', self::EXIT_USAGE);
    }

    /**
     * @return array<int, string> version => file name, ascending
     */
    private function scanFiles(): array
    {
        $files = [];

        foreach (glob(BASE_PATH . '/db/migrations/*.php') as $path) {
            $name = basename($path);
            $version = (int) explode('-', $name, 2)[0];

            if (isset($files[$version])) {
                $this->stop(
                    'Two migration files have the same version ' . $version . ': '
                        . $files[$version] . ' and ' . $name,
                    self::EXIT_USAGE
                );
            }

            $files[$version] = $name;
        }

        if ($files === []) {
            $this->stop('No migration file found.', self::EXIT_USAGE);
        }

        ksort($files);

        return $files;
    }

    private function install(array $files): void
    {
        if (DB::select('SHOW TABLES') !== []) {
            $this->stop('Database is not empty, do not use "new" as version.', self::EXIT_USAGE);
        }

        $init = min(array_keys($files));

        $this->apply($files, [[$init, $init]], 'new', 0);
        $this->finish(1, $init);
    }

    private function currentVersion(): int
    {
        $reason = '';

        try {
            $current = Config::obtain('db_version');
        } catch (Throwable $e) {
            $current = '';
            $reason = ' (' . $e->getMessage() . ')';
        }

        if (! is_int($current) || $current <= 0) {
            $this->stop(
                'The database has no db_version' . $reason
                    . '. On an empty database run "php xcat Migration new" first.',
                self::EXIT_USAGE
            );
        }

        return $current;
    }

    /**
     * @return array<int, array{int, int}> [version to run, db_version after it], in running order
     */
    private function plan(array $files, int $current, int $limit): array
    {
        $versions = array_keys($files);

        if ($limit >= $current) {
            $run = array_filter($versions, static fn (int $v): bool => $v > $current && $v <= $limit);

            return array_map(static fn (int $v): array => [$v, $v], array_values($run));
        }

        $run = array_values(array_filter($versions, static fn (int $v): bool => $v > $limit && $v <= $current));
        rsort($run);

        return array_map(fn (int $v): array => [$v, $this->previous($versions, $v)], $run);
    }

    private function previous(array $versions, int $version): int
    {
        return max(array_filter($versions, static fn (int $v): bool => $v < $version));
    }

    /**
     * @param string $direction "up", "down" or "new" (up, then insert the db_version row)
     *
     * @return int the db_version after the last completed migration
     */
    private function apply(array $files, array $plan, string $direction, int $current): int
    {
        foreach ($plan as $index => [$version, $next]) {
            echo ($direction === 'down' ? 'Rollback to ' : 'Forward to ') . $version . PHP_EOL;

            $error = $this->execute($files[$version], $direction, $next);

            if ($error !== null) {
                $this->report($files[$version], $direction, $error, $current, count($plan) - $index - 1);

                exit(self::EXIT_FAILED);
            }

            $current = $next;
        }

        return $current;
    }

    private function execute(string $name, string $direction, int $next): ?Throwable
    {
        try {
            $object = require BASE_PATH . '/db/migrations/' . $name;

            if (! $object instanceof MigrationInterface) {
                throw new LogicException('The file does not return a MigrationInterface object');
            }

            if ($direction === 'down') {
                $object->down();
            } else {
                $object->up();
            }

            $this->saveVersion($direction, $next);
        } catch (Throwable $e) {
            return $e;
        }

        return null;
    }

    private function saveVersion(string $direction, int $version): void
    {
        if ($direction === 'new') {
            (new Config())->insert([
                'item' => 'db_version',
                'value' => $version,
                'type' => 'int',
                'default' => '2023020100',
            ]);

            return;
        }

        (new Config())->where('item', 'db_version')->update(['value' => $version]);
    }

    private function finish(int $count, int $current): void
    {
        echo 'Migration completed. ' . $count . ' file(s) processed.' . PHP_EOL
            . 'Current database version: ' . $current . PHP_EOL;
    }

    private function report(string $name, string $direction, Throwable $e, int $current, int $skipped): void
    {
        echo PHP_EOL . 'Migration ' . $name . ' failed: ' . $e::class . ': ' . $e->getMessage() . PHP_EOL
            . '  at ' . $this->location($e, $name) . PHP_EOL;

        if ($e instanceof QueryException) {
            echo '  SQL: ' . $e->getSql() . PHP_EOL;
        }

        if ($direction === 'new') {
            echo 'No db_version was written. Statements of ' . $name
                . ' that ran before the failure stay in the database (MySQL/MariaDB cannot roll back DDL).' . PHP_EOL
                . 'Drop and recreate the database empty, then run "php xcat Migration new" again.' . PHP_EOL;

            return;
        }

        $again = $direction === 'up' ? 'run "php xcat Migration latest" again' : 'run the same command again';

        echo 'db_version is ' . $current . ': every migration up to it has completed. '
            . $skipped . ' later migration(s) were not run.' . PHP_EOL
            . 'Running the command again starts ' . $name . ' again from its first statement. Statements of it that ran before the failure stay in the database (MySQL/MariaDB cannot roll back DDL); if the re-run fails on them (for example a table that already exists), restore the database backup taken before the upgrade and upgrade again.' . PHP_EOL
            . 'Fix the cause, then ' . $again . '.' . PHP_EOL;
    }

    private function location(Throwable $e, string $name): string
    {
        $frames = array_merge([['file' => $e->getFile(), 'line' => $e->getLine()]], $e->getTrace());

        foreach ($frames as $frame) {
            if (isset($frame['file'], $frame['line']) && basename($frame['file']) === $name) {
                return $this->relative($frame['file']) . ':' . $frame['line'];
            }
        }

        return $this->relative($e->getFile()) . ':' . $e->getLine();
    }

    private function relative(string $path): string
    {
        return str_replace(BASE_PATH . '/', '', $path);
    }
}
