<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class StringToText extends AbstractMigration
{
    public function up(): void
    {
        $this->table('email_queue')
            ->changeColumn('array', 'text')
            ->save();

        $this->table('shop')
            ->changeColumn('content', 'text')
            ->save();

        $this->table('ticket')
            ->changeColumn('content', 'text')
            ->save();

        $this->table('user')
            ->changeColumn('remark', 'text')
            ->save();

        $this->table('node')
            ->changeColumn('info', 'text')
            ->changeColumn('custom_config', 'text')
            ->save();

        $this->table('announcement')
            ->changeColumn('content', 'text')
            ->changeColumn('markdown', 'text')
            ->save();

        $this->table('user_subscribe_log')
            ->changeColumn('request_user_agent', 'text')
            ->save();
    }

    public function down(): void
    {
        $this->table('email_queue')
            ->changeColumn('array', 'string')
            ->save();

        $this->table('shop')
            ->changeColumn('content', 'string')
            ->save();

        $this->table('ticket')
            ->changeColumn('content', 'string')
            ->save();

        $this->table('user')
            ->changeColumn('remark', 'string')
            ->save();

        $this->table('node')
            ->changeColumn('info', 'string')
            ->changeColumn('custom_config', 'string')
            ->save();

        $this->table('announcement')
            ->changeColumn('content', 'string')
            ->changeColumn('markdown', 'string')
            ->save();

        $this->table('user_subscribe_log')
            ->changeColumn('request_user_agent', 'string')
            ->save();
    }
}
