<?php namespace App\Tests\Unit\Http\Controllers;

use Mockery as m;

class IpnControllerTest extends \Tests\TestCase {

  public function testValidateIpn() {

    $paymentProcessor = m::mock('\App\Domain\Interfaces\PaymentProcessor');
    $paymentProcessor->shouldReceive('process')->once()->with([]);
    $this->app->bind('App\Domain\Interfaces\PaymentProcessor', function() use($paymentProcessor) {
      return $paymentProcessor;
    });

    $request = m::mock(\Illuminate\Http\Request::class);
    $request->shouldReceive('all')->once()->andReturn([]);
    $this->app->bind(\Illuminate\Http\Request::class, function() use($request) {
      return $request;
    });

    $response = $this->get('ipn');

    $response->assertStatus(200);
  }
}

