<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ProductTableUpdate extends AbstractMigration
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
        $table = $this->table('product');
        $table->addColumn('sort', 'integer', array('comment' => '自定义排序', 'after' => 'translate'))
            ->addColumn('rebate_mode', 'integer', array('comment' => '商品返利模式', 'after' => 'status'))
            ->addColumn('rebate_amount', 'integer', array('comment' => '商品自定义返利金额', 'after' => 'status'))
            ->update();
    }
}
