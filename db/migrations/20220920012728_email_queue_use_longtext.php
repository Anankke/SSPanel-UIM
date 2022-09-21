<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class EmailQueueUseLongtext extends AbstractMigration
{
    public function up(): void
    {
        $this->table('email_queue')
            ->changeColumn('array', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
            ->save();
    }

    public function down(): void
    {
        $this->table('email_queue')
            ->changeColumn('array', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR])
            ->save();
    }
}
