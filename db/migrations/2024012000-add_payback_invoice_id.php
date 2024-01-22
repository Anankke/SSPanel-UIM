<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE payback ADD COLUMN IF NOT EXISTS `invoice_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账单ID';
        ");

        return 2024012000;
    }

    public function down(): int
    {
        DB::getPdo()->exec('
            ALTER TABLE payback DROP COLUMN IF EXISTS `invoice_id`;
        ');

        return 2023120700;
    }
};
