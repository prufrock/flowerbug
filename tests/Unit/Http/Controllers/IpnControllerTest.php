<?php

class IpnControllerTest extends \Tests\TestCase {

  public function testValidateIpn() {

    $mockPaymentProcessor = new FakePaymentProcessor();
    $this->app->bind('App\Domain\Interfaces\PaymentProcessor', function() use($mockPaymentProcessor) {
      return $mockPaymentProcessor;
    });
    $response = $this->get('ipn');

    $response->assertStatus(200);
    $this->assertTrue($mockPaymentProcessor->process_called, 'Process was called on FakePaymentProcessor.');
  }
}

class FakePaymentProcessor implements \App\Domain\Interfaces\PaymentProcessor {

  public $process_called = false;

  public function process() {
    $this->process_called = true;
  }
}