<?php
declare (strict_types = 1);

use Phinx\Migration\AbstractMigration;

final class ProductTable extends AbstractMigration
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
        $table->addColumn('type', 'text', array('comment' => '类型'))
            ->addColumn('name', 'text', array('comment' => '名称'))
            ->addColumn('price', 'integer', array('comment' => '售价'))
            ->addColumn('content', 'text', array('comment' => '内容'))
            ->addColumn('translate', 'text', array('comment' => '内容翻译'))
            ->addColumn('html', 'text', array('comment' => '自定义代码'))
            ->addColumn('limit', 'text', array('comment' => '购买限制', 'default' => null, 'null' => true))
            ->addColumn('status', 'integer', array('comment' => '销售状态'))
            ->addColumn('created_at', 'integer', array('comment' => '创建时间'))
            ->addColumn('updated_at', 'integer', array('comment' => '更新时间'))
            ->create();
    }
}
