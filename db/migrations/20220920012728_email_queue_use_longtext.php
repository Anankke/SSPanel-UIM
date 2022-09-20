<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class EmailQueueUseLongtext extends AbstractMigration
{
    public function up(): void
    {
        $this->table('email_queue')
            ->changeColumn('array', 'longtext')
            ->save();
    }

    public function down(): void
    {
        $this->table('email_queue')
            ->changeColumn('array', 'text')
            ->save();
    }
}
