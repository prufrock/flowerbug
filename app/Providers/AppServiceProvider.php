<?php

namespace App\Providers;

use App\Domain\IpnResponder;
use App\Domain\PaymentProcessor;
use App\Services\Locker;
use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot() {
    //
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register() {
    $this->app->bind(
      Locker::class,
      function ($app) {
        return new Locker($app->make(S3Client::class));
      }
    );

    $this->app->when(PaymentProcessor::class)->needs('$responder')->give(IpnResponder::class);
  }
}
