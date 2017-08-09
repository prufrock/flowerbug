<?php namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class PaymentProcessorTest extends TestCase {

  public function testPaymentProcessorRole() {

    $paymentProcessor = new \App\Domain\PaymentProcessor();

    $this->assertTrue(method_exists($paymentProcessor, 'process'));
  }

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\PaymentProcessor::class,
      new \App\Domain\PaymentProcessor()
    );
  }

  public function testValidMessage() {

    $ipnResponder = m::mock('\Application\Domain\IpnResponder');
    $processor = new \App\Domain\PaymentProcessor($ipnResponder);

    $ipnResponder->shouldReceive('create')->once();

    $processor->process([]);
  }
}
