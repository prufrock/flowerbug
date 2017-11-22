<?php namespace Tests\Unit\Providers;

use Aws\Ses\SesClient;
use Tests\TestCase;

class SesServiceProviderTest extends TestCase {

  public function testInjection() {

    $object = $this->app->make(SesClient::class);

    $this->assertEquals(SesClient::class, get_class($object));
  }
}
