<?php

namespace Tests\Unit\Providers;

use Aws\S3\S3Client;
use Tests\TestCase;

class S3ServiceProviderTest extends TestCase {

  public function testInjection() {

    $object = $this->app->make(S3Client::class);

    $this->assertEquals(S3Client::class, get_class($object));
  }
}
