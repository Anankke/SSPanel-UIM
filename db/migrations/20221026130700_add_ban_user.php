<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddBanUser extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->table('user')->hasColumn('is_banned')) {
            $this->table('user')
                ->addColumn('is_banned', 'integer', [ 'comment' => '是否封禁', 'default' => 0 ])
                ->save();
        }
        if (! $this->table('user')->hasColumn('banned_reason')) {
            $this->table('user')
                ->addColumn('banned_reason', 'string', [ 'comment' => '封禁理由', 'default' => '' ])
                ->save();
        }
        if ($this->table('user')->hasColumn('is_hide')) {
            $this->table('user')
                ->removeColumn('is_hide')
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('user')
            ->addColumn('is_hide', 'integer', [ 'default' => 0 ])
            ->save();
    }
}
