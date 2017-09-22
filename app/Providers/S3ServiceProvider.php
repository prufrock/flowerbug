<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Aws\S3\S3Client;

class S3ServiceProvider extends ServiceProvider {

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
  public function register() {
    $this->app->singleton(
      S3Client::class,
      function () {
        return S3Client::factory();
      }
    );
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides() {
    return [S3Client::class];
  }
}
