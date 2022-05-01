<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddStatisticsIndex extends AbstractMigration
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
        $table = $this->table('statistics');
        $table->addIndex(array('user_id'), array('name' => 'user_id'))
            ->addIndex(array('node_id'), array('name' => 'node_id'))
            ->update();
    }
}
