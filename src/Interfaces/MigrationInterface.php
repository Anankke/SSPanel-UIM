<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * Database migration interface
 *
 * Any migration file(object) must implement this interface. up() and down() are free,
 * you could do anything you want. A migration that cannot complete must throw: the
 * command stops at the failed migration, with db_version at the last completed one.
 * The return values are no longer used.
 */
interface MigrationInterface
{
    /**
     * Migrate database schema
     *
     * @return int current version (not used by the command)
     */
    public function up(): int;

    /**
     * Rollback all changes caused by up()
     *
     * @return int previous version (not used by the command)
     */
    public function down(): int;
}
