<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUserDarkMode extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->table('user')->hasColumn('is_dark_mode')) {
            $this->table('user')
                ->addColumn('is_dark_mode', 'integer', ['default' => 0])
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('user')
            ->removeColumn('is_dark_mode')
            ->save();
    }
}
