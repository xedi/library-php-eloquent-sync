<?php

namespace Xedi\BasicSync;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Xedi\BasicSync\SyncEntities;

/**
 * Register the BasicSync Macro with the HasMany Relationship
 *
 * @package Xedi\BasicSync
 * @author  Chris Smith <chris@xedi.com>
 */
class HasManyServiceProvider extends ServiceProvider
{
    /**
     * Mixin the sync method
     *
     * @return void
     */
    public function boot()
    {
        HasMany::macro('sync', function ($data, $deleting = true) {
            return (new SyncEntities($this))
                ->handle($data, $deleting);
        });
    }
}
