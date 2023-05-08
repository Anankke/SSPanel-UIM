<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("ALTER TABLE paylist ADD COLUMN IF NOT EXISTS `gateway` varchar(255) NOT NULL DEFAULT '';");

        return 2023031701;
    }

    public function down(): int
    {
        DB::getPdo()->exec('ALTER TABLE paylist DROP COLUMN IF EXISTS `gateway`;');

        return 2023031700;
    }
};
