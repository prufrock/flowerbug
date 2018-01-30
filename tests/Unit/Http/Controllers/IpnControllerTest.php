<?php namespace App\Tests\Unit\Http\Controllers;

use Mockery as m;

class IpnControllerTest extends \Tests\TestCase {

  public function testValidateIpn() {

    $paymentProcessor = m::mock(\App\Domain\PaymentProcessor::class);
    $paymentProcessor->shouldReceive('process')->once()->with(m::type(\App\Domain\IpnMessage::class));
    $this->app->bind(\App\Domain\PaymentProcessor::class, function() use($paymentProcessor) {
      return $paymentProcessor;
    });

    $request = m::mock(\Illuminate\Http\Request::class);
    $request->shouldReceive('all')->andReturn([]);
    $this->app->bind(\Illuminate\Http\Request::class, function() use($request) {
      return $request;
    });

    $response = $this->post('/api/ipn');

    $response->assertStatus(200);
  }
}

