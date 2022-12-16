<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUserUseNewShop extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->table('user')->hasColumn('use_new_shop')) {
            $this->table('user')
                ->addColumn('use_new_shop', 'smallinteger', [ 'comment' => '是否启用新商店', 'null' => false, 'default' => 0 ])
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('user')
            ->removeColumn('use_new_shop')
            ->save();
    }
}
