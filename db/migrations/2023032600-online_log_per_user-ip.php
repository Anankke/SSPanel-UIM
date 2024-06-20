<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS online_log (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT UNSIGNED NOT NULL,
                ip INET6 NOT NULL,
                node_id INT UNSIGNED NOT NULL,
                first_time INT UNSIGNED NOT NULL,
                last_time INT UNSIGNED NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY (user_id, ip),
                KEY (last_time)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $pdo->exec('DROP TABLE IF EXISTS alive_ip');

        return 2023032600;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS alive_ip (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                nodeid INT(11) DEFAULT NULL,
                userid INT(11) DEFAULT NULL,
                ip VARCHAR(255) DEFAULT NULL,
                datetime BIGINT(20) DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $pdo->exec('DROP TABLE IF EXISTS online_log');

        return 2023031701;
    }
};
