<?php namespace App\Tests\Unit\Http\Controllers;

use Mockery as m;

class IpnControllerTest extends \Tests\TestCase {

  public function testValidateIpn() {

    $paymentProcessor = m::mock('\App\Domain\Interfaces\PaymentProcessor');
    $paymentProcessor->shouldReceive('process')->once();
    $this->app->bind('App\Domain\Interfaces\PaymentProcessor', function() use($paymentProcessor) {
      return $paymentProcessor;
    });
    $response = $this->get('ipn');

    $response->assertStatus(200);
  }
}

