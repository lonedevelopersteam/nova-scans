<?php

namespace App\Providers;

use App\Services\Impl\MangaServiceImpl;
use App\Services\MangaService;
use Illuminate\Support\ServiceProvider;

class MangaServiceProvider extends ServiceProvider
{
    public array $singletons = [
        MangaService::class => MangaServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MangaService::class];
    }

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
