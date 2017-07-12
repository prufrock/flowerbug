<?php

class IpnControllerTest extends \Tests\TestCase {

  public function testValidateIpn() {

    $response = $this->get('ipn');

    $response->assertStatus(200);
  }
}