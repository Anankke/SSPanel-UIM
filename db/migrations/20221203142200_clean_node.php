<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CleanNode extends AbstractMigration
{
    public function up(): void
    {
        if ($this->table('node')->hasColumn('load')) {
            $this->table('node')
                ->removeColumn('load')
                ->save();
        }
        if ($this->table('node')->hasColumn('uptime')) {
            $this->table('node')
                ->removeColumn('uptime')
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('node')
            ->addColumn('load', 'string', ['default' => ''])
            ->addColumn('uptime', 'integer', ['default' => 0])
            ->save();
    }
}
