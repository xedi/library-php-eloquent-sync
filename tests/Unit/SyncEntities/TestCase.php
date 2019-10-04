<?php

namespace Tests\Unit\SyncEntities;

use Tests\TestCase as BaseTestCase;
use Xedi\Eloquent\Sync\HasManyServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            HasManyServiceProvider::class,
        ];
    }
}
