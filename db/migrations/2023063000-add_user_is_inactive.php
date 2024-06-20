<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user DROP COLUMN IF EXISTS `t`;
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `is_inactive` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否处于闲置状态';
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `last_use_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '最后使用时间';
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `last_login_time` int(11) unsigned DEFAULT 0 COMMENT '最后登录时间';
            ALTER TABLE user ADD KEY IF NOT EXISTS `is_inactive` (`is_inactive`);
        ");

        return 2023063000;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `t` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '最后使用时间';
            ALTER TABLE user DROP COLUMN IF EXISTS `is_inactive`;
            ALTER TABLE user DROP COLUMN IF EXISTS `last_use_time`;
            ALTER TABLE user DROP COLUMN IF EXISTS `last_login_time`;
            ALTER TABLE user DROP KEY IF EXISTS `is_inactive`;
        ");

        return 2023061800;
    }
};
