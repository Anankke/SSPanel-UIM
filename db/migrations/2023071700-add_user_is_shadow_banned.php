<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `is_shadow_banned` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否处于账户异常状态';
            ALTER TABLE user MODIFY COLUMN `is_dark_mode` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否启用暗黑模式';
            ALTER TABLE user MODIFY COLUMN `is_inactive` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否处于闲置状态';
            ALTER TABLE user DROP COLUMN IF EXISTS `use_new_shop`;
        ");

        return 2023071700;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user DROP COLUMN IF EXISTS `is_shadow_banned`;
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `use_new_shop` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '是否启用新商店',
        ");

        return 2023071600;
    }
};
