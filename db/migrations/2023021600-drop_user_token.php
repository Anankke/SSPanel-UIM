<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec('DROP TABLE IF EXISTS `user_token`');

        return 2023021600;
    }

    public function down(): int
    {
        DB::getPdo()->exec(
            "CREATE TABLE IF NOT EXISTS `user_token` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `token` varchar(255) DEFAULT NULL,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `create_time` bigint(20) unsigned DEFAULT NULL,
                `expire_time` bigint(20) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );

        return 2023020100;
    }
};
