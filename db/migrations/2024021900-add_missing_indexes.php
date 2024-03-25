<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec('
        ALTER TABLE config ADD KEY IF NOT EXISTS `item` (`item`);
        ALTER TABLE config ADD KEY IF NOT EXISTS `class` (`class`);
        ALTER TABLE config ADD KEY IF NOT EXISTS `is_public` (`is_public`);
        ALTER TABLE hourly_usage ADD KEY IF NOT EXISTS `date` (`date`);
        ALTER TABLE node ADD UNIQUE KEY IF NOT EXISTS `password` (`password`);
        ALTER TABLE node ADD KEY IF NOT EXISTS `is_dynamic_rate` (`is_dynamic_rate`);
        ALTER TABLE node ADD KEY IF NOT EXISTS `bandwidthlimit_resetday` (`bandwidthlimit_resetday`);
        ALTER TABLE online_log ADD KEY IF NOT EXISTS `node_id` (`node_id`);
        ALTER TABLE payback ADD KEY IF NOT EXISTS `userid` (`userid`);
        ALTER TABLE payback ADD KEY IF NOT EXISTS `ref_by` (`ref_by`);
        ALTER TABLE payback ADD KEY IF NOT EXISTS `invoice_id` (`invoice_id`);
        ALTER TABLE paylist ADD UNIQUE KEY IF NOT EXISTS `tradeno` (`tradeno`);
        ALTER TABLE paylist ADD KEY IF NOT EXISTS `status` (`status`);
        ALTER TABLE paylist ADD KEY IF NOT EXISTS `invoice_id` (`invoice_id`);
        ALTER TABLE subscribe_log ADD KEY IF NOT EXISTS `type` (`type`);
        ALTER TABLE ticket ADD KEY IF NOT EXISTS `type` (`type`);
        ALTER TABLE user ADD KEY IF NOT EXISTS `is_shadow_banned` (`is_shadow_banned`);');

        return 2024021900;
    }

    public function down(): int
    {
        return 2024021900;
    }
};
