<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DeleteOldTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('blockip')
        ->drop()
        ->update();

        $this->table('bought')
        ->drop()
        ->update();

        $this->table('code')
        ->drop()
        ->update();

        $this->table('detect_ban_log')
        ->drop()
        ->update();

        $this->table('paylist')
        ->drop()
        ->update();

        $this->table('shop')
        ->drop()
        ->update();

        $this->table('unblockip')
        ->drop()
        ->update();

        $this->table('user_hourly_usage')
        ->drop()
        ->update();
    }
}
