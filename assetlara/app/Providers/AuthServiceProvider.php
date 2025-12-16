<?php

namespace App\Providers;

use App\Models\Asset;
use App\Policies\AssetPolicy;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Map the Asset Model to the AssetPolicy
        Asset::class => AssetPolicy::class,
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
