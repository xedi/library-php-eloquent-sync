<?php

namespace Tests;

use Orchestra\Testbench\Concerns\Database\WithSqlite;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Base TestCase
 *
 * @package Xedi\BasicSync
 * @author  Chris Smith <chris@xedi.com>
 */
abstract class TestCase extends BaseTestCase
{
    use WithSqlite;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->withFactories(__DIR__ . '/Factories');
    }
}
