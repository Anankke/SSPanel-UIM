<?php
declare (strict_types = 1);

use Phinx\Migration\AbstractMigration;

final class AddProductStockField extends AbstractMigration
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
        $table->addColumn('sales', 'integer', array('comment' => '商品销售数', 'after' => 'content'))
            ->addColumn('stock', 'integer', array('comment' => '商品库存', 'after' => 'content'))
            ->update();
    }
}
