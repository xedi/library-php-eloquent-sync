<?php

namespace Xedi\BasicSync;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Xedi\BasicSync\SyncMixin;

/**
 * Register the BasicSync Mixin with the HasMany Relationship
 *
 * @package Xedi\BasicSync
 * @author  Chris Smith <chris@xedi.com>
 */
class BasicSyncServiceProvider extends ServiceProvider
{
    private const OVERWRITE_METHODS = false;

    /**
     * Mixin the sync method
     *
     * @return void
     */
    public function register()
    {
        HasMany::mixin(new SyncMixin(), self::OVERWRITE_METHODS);
    }
}
