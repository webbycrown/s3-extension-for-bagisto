<?php

namespace Webbycrown\S3Extension\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Webbycrown\S3Extension\Observers\S3Observer;
use Webkul\Core\Models\CoreConfig;

class AwsS3ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        CoreConfig::observe(S3Observer::class);
    }
    
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }
    
    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );
    }
}
