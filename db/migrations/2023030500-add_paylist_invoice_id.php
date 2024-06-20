<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec('ALTER TABLE paylist ADD COLUMN IF NOT EXISTS `invoice_id` int(11) DEFAULT 0;');

        return 2023030500;
    }

    public function down(): int
    {
        DB::getPdo()->exec('ALTER TABLE paylist DROP COLUMN IF EXISTS `invoice_id`;');

        return 2023021600;
    }
};
