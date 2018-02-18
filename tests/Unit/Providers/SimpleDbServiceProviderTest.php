<?php

namespace Tests\Unit\Providers;

use Aws\SimpleDb\SimpleDbClient;
use Tests\TestCase;

class SimpleDbServiceProviderTest extends TestCase
{
    public function testInjection()
    {
        $object = $this->app->make(SimpleDbClient::class);

        $this->assertEquals(SimpleDbClient::class, get_class($object));
    }
}
