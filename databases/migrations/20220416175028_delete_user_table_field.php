<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DeleteUserTableField extends AbstractMigration
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
        $table = $this->table('user');
        $table->removeColumn('last_detect_ban_time')
            ->removeColumn('all_detect_number')
            ->removeColumn('is_hide')
            ->removeColumn('auto_reset_day')
            ->removeColumn('auto_reset_bandwidth')
            ->save();
    }
}
