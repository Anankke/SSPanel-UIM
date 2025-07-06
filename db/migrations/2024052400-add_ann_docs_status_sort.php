<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE announcement ADD COLUMN IF NOT EXISTS `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '公告状态';
            ALTER TABLE announcement ADD COLUMN IF NOT EXISTS `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '公告排序';
            ALTER TABLE announcement ADD KEY IF NOT EXISTS `status` (`status`);
            ALTER TABLE announcement ADD KEY IF NOT EXISTS `sort` (`sort`);
            ALTER TABLE docs ADD COLUMN IF NOT EXISTS `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '文档状态';
            ALTER TABLE docs ADD COLUMN IF NOT EXISTS `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '文档排序';
            ALTER TABLE docs ADD KEY IF NOT EXISTS `status` (`status`);
            ALTER TABLE docs ADD KEY IF NOT EXISTS `sort` (`sort`);
        ");

        return 2024052400;
    }

    public function down(): int
    {
        return 2024052400;
    }
};
