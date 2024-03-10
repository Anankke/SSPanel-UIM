<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user DROP COLUMN IF EXISTS `forbidden_ip`;
            ALTER TABLE user DROP COLUMN IF EXISTS `forbidden_port`;
            ALTER TABLE user MODIFY COLUMN `api_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'API Token';
            ALTER TABLE user MODIFY COLUMN `uuid` uuid NOT NULL COMMENT 'UUID';
            ALTER TABLE user ADD UNIQUE KEY IF NOT EXISTS `passwd` (`passwd`);
        ");

        return 2024031000;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `forbidden_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '禁止访问IP';
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `forbidden_port` varchar(255) NOT NULL DEFAULT '' COMMENT '禁止访问端口';
            ALTER TABLE user MODIFY COLUMN `api_token` char(36) NOT NULL DEFAULT '' COMMENT 'API 密钥';
            ALTER TABLE user MODIFY COLUMN `uuid` char(36) NOT NULL COMMENT 'UUID';
        ");

        return 2024030300;
    }
};
