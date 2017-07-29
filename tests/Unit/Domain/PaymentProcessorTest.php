<?php namespace Tests\Unit\Domain;

use Tests\TestCase;

class PaymentProcessorTest extends TestCase {

  public function testPaymentProcessorRole() {

    $paymentProcessor = new \App\Domain\PaymentProccesor();

    $this->assertTrue(method_exists($paymentProcessor, 'process'));
  }

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\PaymentProccesor::class,
      new \App\Domain\PaymentProccesor()
    );
  }
}
