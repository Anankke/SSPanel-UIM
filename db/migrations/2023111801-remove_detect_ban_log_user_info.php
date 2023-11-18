<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec('
            ALTER TABLE detect_ban_log DROP COLUMN IF EXISTS `user_name`;
            ALTER TABLE detect_ban_log DROP COLUMN IF EXISTS `email`;
        ');

        return 2023111801;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE detect_ban_log ADD COLUMN IF NOT EXISTS `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名';
            ALTER TABLE detect_ban_log ADD COLUMN IF NOT EXISTS `email` varchar(255) NOT NULL DEFAULT '' COMMENT '用户邮箱';
        ");

        return 2023111800;
    }
};
