<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $db = DB::getPdo();

        $db->exec("
            INSERT IGNORE INTO `config` (`item`, `value`, `class`, `is_public`, `type`, `default`, `mark`)
            VALUES ('enable_telemetry', '1', 'system', '0', 'bool', '1', '系统遥测统计')
        ");

        return 2025070500;
    }

    public function down(): int
    {
        $db = DB::getPdo();

        $db->exec("DELETE FROM `config` WHERE `item` = 'enable_telemetry'");

        return 2024040500;
    }
};
