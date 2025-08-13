<?php

namespace App\Providers;

use App\Services\CacheService;
use App\Services\Impl\CacheServiceImpl;
use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    public array $singletons = [
        CacheService::class => CacheServiceImpl::class,
    ];

    public function provides(): array
    {
        return [CacheService::class];
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
