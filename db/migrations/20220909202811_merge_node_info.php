<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MergeNodeInfo extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->table('node')->hasColumn('load')) {
            $this->table('node')
                ->addColumn('load', 'string')
                ->save();
        }
        if (! $this->table('node')->hasColumn('uptime')) {
            $this->table('node')
                ->addColumn('uptime', 'integer')
                ->save();
        }
        if ($this->hasTable('node_info')) {
            $this->table('node_info')->drop()->update();
        }
    }

    public function down(): void
    {
        $this->table('node')
            ->removeColumn('load')
            ->removeColumn('uptime')
            ->save();
        $this->table('node_info', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
            ->addColumn('node_id', 'integer', [])
            ->addColumn('uptime', 'float', [])
            ->addColumn('load', 'string', [])
            ->addColumn('log_time', 'integer', [])
            ->addIndex([ 'node_id' ])
            ->addForeignKey('node_id', 'node', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();
    }
}
