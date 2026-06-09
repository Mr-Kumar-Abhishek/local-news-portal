<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Base test case for database tests.
 * Uses migrateOnce and refresh=false to avoid SQLite lock contention
 * between test classes. Tables are migrated once and reused; seeds run
 * once (subsequent seed calls are no-ops since data already exists).
 */
abstract class DatabaseTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait {
        setUpSeed as traitSetUpSeed;
    }

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh      = false;
    protected $seedOnce     = true;
    protected $namespace   = null;
    protected $seed        = \App\Database\Seeds\HindBiharSeeder::class;

    /**
     * Override setUpSeed to prevent re-seeding across test classes.
     * The #[AfterClass] in the trait resets $doneSeed, so we track
     * our own static flag to ensure seeding happens only once globally.
     */
    private static $globalSeedDone = false;

    protected function setUpSeed(): void
    {
        if (self::$globalSeedDone === false) {
            $this->traitSetUpSeed();
            self::$globalSeedDone = true;
        }
    }
}
