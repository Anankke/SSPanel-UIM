<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use App\Services\DB;
use Tests\TestDatabase;

abstract class TestCase extends BaseTestCase
{
    
    protected bool $useDatabase = false;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        if ($this->useDatabase) {
            $this->setUpDatabase();
        }
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    protected function setUpDatabase(): void
    {
        try {
            TestDatabase::init();
            $this->createTestTables();
        } catch (\Exception $e) {
            $this->markTestSkipped('Database connection not available: ' . $e->getMessage());
        }
    }
    
    protected function createTestTables(): void
    {
        // Override in test classes that need specific tables
    }
}