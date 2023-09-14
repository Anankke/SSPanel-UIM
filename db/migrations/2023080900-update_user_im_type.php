<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user MODIFY COLUMN `im_type` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '联系方式类型';
            UPDATE user SET `im_value` = '' WHERE `im_value` = '0';
        ");

        return 2023080900;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user MODIFY COLUMN `im_type` smallint(6) unsigned NOT NULL DEFAULT 1 COMMENT '联系方式类型';
        ");

        return 2023072000;
    }
};
