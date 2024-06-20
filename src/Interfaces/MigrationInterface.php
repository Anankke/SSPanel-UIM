<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * Database migration interface
 *
 * Any migration file(object) must implement this interface. up() and down() are free,
 * you could do anything you want but do not throw any exceptions in them.
 */
interface MigrationInterface
{
    /**
     * Migrate database schema
     *
     * @return int current version
     */
    public function up(): int;

    /**
     * Rollback all changes caused by up()
     *
     * @return int previous version
     */
    public function down(): int;
}
