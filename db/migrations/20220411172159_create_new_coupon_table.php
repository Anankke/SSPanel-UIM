<?php
declare (strict_types = 1);

use Phinx\Migration\AbstractMigration;

final class CreateNewCouponTable extends AbstractMigration
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
        $table = $this->table('coupon');
        $table->addColumn('coupon', 'text', array('comment' => '优惠码'))
            ->addColumn('discount', 'text', array('comment' => '折扣额度'))
            ->addColumn('time_limit', 'text', array('comment' => '时间限制'))
            ->addColumn('product_limit', 'integer', array('comment' => '商品使用范围限制'))
            ->addColumn('user_limit', 'integer', array('comment' => '单用户使用最大次数'))
            ->addColumn('total_limit', 'integer', array('comment' => '全部用户累计使用最大次数'))
            ->addColumn('use_count', 'integer', array('comment' => '使用计数'))
            ->addColumn('amount_count', 'text', array('comment' => '折扣金额计数'))
            ->addColumn('created_at', 'integer', array('comment' => '创建时间'))
            ->addColumn('updated_at', 'integer', array('comment' => '更新时间'))
            ->addColumn('expired_at', 'integer', array('comment' => '过期时间'))
            ->create();
    }
}
