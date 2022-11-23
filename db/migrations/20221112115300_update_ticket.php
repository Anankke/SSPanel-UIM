<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateTicket extends AbstractMigration
{
    public function up(): void
    {
        if ($this->table('ticket')->hasColumn('rootid')) {
            $this->table('ticket')
                ->changeColumn('content', 'json', [ 'comment' => '工单内容', 'default' => '' ])
                ->changeColumn('status', 'string', [ 'comment' => '工单状态', 'default' => '' ])
                ->addColumn('type', 'string', [ 'comment' => '工单类型', 'default' => 'other' ])
                ->removeColumn('rootid')
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('ticket')
            ->changeColumn->addColumn('content', 'text', [])
            ->changeColumn('status', 'integer', [ 'default' => 1 ])
            ->removeColumn('type')
            ->addColumn('rootid', 'biginteger', [])
            ->save();
    }
}
