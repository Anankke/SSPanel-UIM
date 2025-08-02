<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Models\MFADevice;
use App\Models\User;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            CREATE TABLE `mfa_devices` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `userid` int(11) unsigned NOT NULL COMMENT '用户ID',
                `body` text NOT NULL COMMENT '密钥内容',
                `name` varchar(255) DEFAULT NULL COMMENT '设备名称',
                `rawid` varchar(255) DEFAULT NULL COMMENT '设备ID',
                `created_at` datetime NOT NULL COMMENT '创建时间',
                `used_at` datetime DEFAULT NULL COMMENT '上次使用时间',
                `type` varchar(50) DEFAULT NULL COMMENT '设备类型',
                PRIMARY KEY (`id`),
                KEY `userid` (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $users = (new User())->where('ga_enable', 1)->get();

        foreach ($users as $user) {
            $token = $user->ga_token;

            $MFADevice = new MFADevice();
            $MFADevice->userid = $user->id;
            $MFADevice->name = 'TOTP';
            $MFADevice->rawid = 'TOTP';
            $MFADevice->body = json_encode(['token' => $token]);
            $MFADevice->type = 'totp';
            $MFADevice->created_at = date('Y-m-d H:i:s');
            $MFADevice->save();
        }

        DB::getPdo()->exec('ALTER TABLE `user` DROP COLUMN `ga_enable`, DROP COLUMN `ga_token`;');

        return 2025073100;
    }

    public function down(): int
    {
        DB::getPdo()->exec('
            DROP TABLE IF EXISTS `mfa_devices`;
        ');

        return 2024061600;
    }
};