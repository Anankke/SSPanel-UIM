<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DefaultAnnouncement extends AbstractMigration
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
    public function up(): void
    {
        $singleRow = [
            'date' => date('Y-m-d H:i:s', time()),
            'content' => '管理员很懒，还没有公告哈',
            'markdown' => '管理员很懒，还没有公告哈',
        ];

        $table = $this->table('announcement');
        $table->insert($singleRow);
        $table->saveData();
    }
}
