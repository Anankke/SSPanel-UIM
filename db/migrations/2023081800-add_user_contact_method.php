<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE user ADD COLUMN IF NOT EXISTS `contact_method` smallint(6) NOT NULL DEFAULT 1 COMMENT '偏好的联系方式';
        ");

        return 2023081800;
    }

    public function down(): int
    {
        DB::getPdo()->exec('
            ALTER TABLE user DROP COLUMN IF EXISTS `contact_method`;
        ');

        return 2023080900;
    }
};
