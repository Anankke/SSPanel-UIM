<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec('
            ALTER TABLE user_invite_code DROP COLUMN IF EXISTS `created_at`;
            ALTER TABLE user_invite_code DROP COLUMN IF EXISTS `updated_at`;
        ');

        return 2023111800;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user_invite_code ADD COLUMN IF NOT EXISTS `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '创建时间';
            ALTER TABLE user_invite_code ADD COLUMN IF NOT EXISTS `updated_at` timestamp NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '更新时间';
        ");

        return 2023111700;
    }
};
