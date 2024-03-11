<?php

namespace Webbycrown\S3Extension\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Webbycrown\S3Extension\Observers\S3Observer;
use Webkul\Core\Models\CoreConfig;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // CoreConfig::observe(S3Observer::class);
    }
}
