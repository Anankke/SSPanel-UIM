<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CustomConfigJson extends AbstractMigration
{
    public function up(): void
    {
        $this->table('node')
            ->changeColumn('custom_config', 'json', [ 'comment' => '自定义配置', 'default' => '{}' ])
            ->save();
    }

    public function down(): void
    {
        $this->table('node')
            ->changeColumn('custom_config', 'text')
            ->save();
    }
}
