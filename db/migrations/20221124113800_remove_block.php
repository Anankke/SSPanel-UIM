<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveBlock extends AbstractMigration
{
    public function up(): void
    {
        if ($this->hasTable('blockip')) {
            $this->table('blockip')->drop()->update();
        }
        if ($this->hasTable('unblockip')) {
            $this->table('unblockip')->drop()->update();
        }
    }

    public function down(): void
    {
        $this->table('blockip', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('nodeid', 'integer', [])
            ->addColumn('ip', 'string', [])
            ->addColumn('datetime', 'biginteger', [])
            ->create();

        $this->table('unblockip', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('ip', 'string', [])
            ->addColumn('datetime', 'biginteger', [])
            ->addColumn('userid', 'biginteger', [])
            ->create();
    }
}
