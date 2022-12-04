<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveAnnMarkdown extends AbstractMigration
{
    public function up(): void
    {
        if ($this->table('announcement')->hasColumn('markdown')) {
            $this->table('announcement')
                ->removeColumn('markdown')
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('announcement')
            ->addColumn('markdown', 'text', [])
            ->save();
    }
}
