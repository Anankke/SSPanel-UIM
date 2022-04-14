<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class GiftCardTable extends AbstractMigration
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
        $table = $this->table('gift_card');
        $table->addColumn('card', 'text', array('comment' => '卡号'))
            ->addColumn('balance', 'integer', array('comment' => '余额'))
            ->addColumn('created_at', 'integer', array('comment' => '创建时间'))
            ->addColumn('status', 'integer', array('comment' => '使用状态'))
            ->addColumn('used_at', 'integer', array('comment' => '使用时间'))
            ->addColumn('use_user', 'integer', array('comment' => '使用用户'))
            ->create();
    }
}
