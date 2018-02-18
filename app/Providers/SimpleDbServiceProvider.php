<?php

namespace App\Providers;

use Aws\SimpleDb\SimpleDbClient;
use Illuminate\Support\ServiceProvider;

class SimpleDbServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SimpleDbClient::class, function () {
            return SimpleDbClient::factory(['region' => config('flowerbug.aws_region')]);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SimpleDbClient::class];
    }
}
