<?php 

namespace App\Providers;

use Aws\Ses\SesClient;
use Illuminate\Support\ServiceProvider;

class SesServiceProvider extends ServiceProvider
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
        $this->app->singleton(SesClient::class, function () {
            return SesClient::factory(['region' => config('flowerbug.aws_region')]);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SesClient::class];
    }
}
