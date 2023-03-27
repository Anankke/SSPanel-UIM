<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        $pdo->exec('
            CREATE TABLE online_log (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT UNSIGNED NOT NULL,
                ip INET6 NOT NULL,
                node_id INT UNSIGNED NOT NULL,
                first_time INT UNSIGNED NOT NULL,
                last_time INT UNSIGNED NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY (user_id, ip)
            )
        ');

        $pdo->exec('
            INSERT INTO online_log (user_id, ip, node_id, first_time, last_time)
                SELECT
                    userid,
                    CASE
                        WHEN IS_IPV4(ip) = 1 THEN CONCAT("::ffff:", ip)
                        WHEN IS_IPV6(ip) = 1 THEN ip
                        ELSE NULL
                    END AS new_ip,
                    nodeid,
                    MIN(datetime) AS first_time,
                    MAX(datetime) AS last_time
                FROM
                    alive_ip
                WHERE
                    userid IS NOT NULL
                    AND nodeid IS NOT NULL
                    AND datetime IS NOT NULL
                GROUP BY
                    userid, ip
                HAVING
                    new_ip IS NOT NULL
        ');

        $pdo->exec('DROP TABLE alive_ip');

        return 2023032500;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        $pdo->exec('
            CREATE TABLE alive_ip (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                nodeid INT(11) DEFAULT NULL,
                userid INT(11) DEFAULT NULL,
                ip VARCHAR(255) DEFAULT NULL,
                datetime BIGINT(20) DEFAULT NULL,
                PRIMARY KEY (id)
            ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        $pdo->exec('
            INSERT INTO alive_ip (nodeid, userid, ip, datetime)
                SELECT node_id, user_id, ip, first_time AS datetime FROM online_log
                UNION
                SELECT node_id, user_id, ip, last_time AS datetime FROM online_log
        ');

        $pdo->exec('DROP TABLE online_log');

        return 2023031701;
    }
};
