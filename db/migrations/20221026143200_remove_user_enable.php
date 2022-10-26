<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveUserEnable extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->table('user')->hasColumn('enable')) {
            $this->table('user')
                ->removeColumn('enable')
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('user')
            ->addColumn('enable', 'boolean', [ 'comment' => '是否启用', 'default' => true ])
            ->save();
    }
}
