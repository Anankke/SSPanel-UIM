<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FingerprintTable extends AbstractMigration
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
        $table = $this->table('fingerprint');
        $table->addColumn('user_id', 'integer', array('comment' => '用户编号'))
            ->addColumn('fingerprint', 'string', array('comment' => '浏览器指纹', 'limit' => 255))
            ->addIndex(array('fingerprint'))
            ->addColumn('created_at', 'integer', array('comment' => '创建时间'))
            ->create();
    }
}
