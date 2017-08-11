<?php namespace Tests\Unit\Domain;

use Tests\TestCase;

class IpnResponderTest extends TestCase {

  public function testNew() {

    $responder = new \App\Domain\IpnResponder();

    $this->assertInstanceOf(
      \App\Domain\IpnResponder::class,
      $responder
    );
  }
}
