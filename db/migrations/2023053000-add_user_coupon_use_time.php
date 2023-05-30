<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("ALTER TABLE user_coupon ADD COLUMN IF NOT EXISTS `use_count` int(11) NOT NULL DEFAULT 0 COMMENT '累计使用数';");

        return 2023053000;
    }

    public function down(): int
    {
        DB::getPdo()->exec('ALTER TABLE user_coupon DROP COLUMN IF EXISTS `use_count`;');

        return 2023050800;
    }
};
