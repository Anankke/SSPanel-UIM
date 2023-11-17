<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec('
            ALTER TABLE user DROP COLUMN IF EXISTS `expire_in`;
        ');

        return 2023082000;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `expire_in` datetime NOT NULL DEFAULT '2199-01-01 00:00:00' COMMENT '账户过期时间';
        ");

        return 2023081800;
    }
};
