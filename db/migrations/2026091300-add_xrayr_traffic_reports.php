<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            CREATE TABLE `xrayr_traffic_reports` (
                `report_id` char(32) NOT NULL,
                `node_id` int(11) NOT NULL,
                `created_at` datetime NOT NULL,
                PRIMARY KEY (`report_id`, `node_id`),
                KEY `created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        return 2026091300;
    }

    public function down(): int
    {
        DB::getPdo()->exec('DROP TABLE IF EXISTS `xrayr_traffic_reports`;');

        return 2025073100;
    }
};
