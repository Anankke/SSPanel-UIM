<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    private const UP = <<< END
        DROP TABLE IF EXISTS `user_token`;   
END;

    private const DOWN = <<< END
        CREATE TABLE `user_token` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `token` varchar(255) DEFAULT NULL,
            `user_id` bigint(20) unsigned DEFAULT NULL,
            `create_time` bigint(20) unsigned DEFAULT NULL,
            `expire_time` bigint(20) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;   
END;

    public function up(): void
    {
        DB::getPdo()->exec(self::UP);
    }

    public function down(): void
    {
        DB::getPdo()->exec(self::DOWN);
    }
};
