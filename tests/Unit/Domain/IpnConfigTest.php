<?php 

namespace Tests\Unit\Domain;

use Tests\TestCase;

class IpnConfigTest extends TestCase
{
    public function testNew()
    {
        $this->assertInstanceOf(\App\Domain\IpnConfig::class, resolve(\App\Domain\IpnConfig::class));
    }
}
