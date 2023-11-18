<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec('
            ALTER TABLE node DROP COLUMN IF EXISTS `info`;
            ALTER TABLE node DROP COLUMN IF EXISTS `status`;
        ');

        return 2023111700;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE node ADD COLUMN IF NOT EXISTS `info` varchar(255) NOT NULL DEFAULT '' COMMENT '节点信息';
            ALTER TABLE node ADD COLUMN IF NOT EXISTS `status` varchar(255) NOT NULL DEFAULT '' COMMENT '节点状态';
        ");

        return 2023102200;
    }
};
