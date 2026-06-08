<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Base test case for database tests.
 * Uses migrateOnce to avoid SQLite lock contention between test classes.
 */
abstract class DatabaseTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $namespace   = null;
    protected $seed        = \App\Database\Seeds\HindBiharSeeder::class;
}
