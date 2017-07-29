<?php namespace Tests\Unit\Domain;

use Tests\TestCase;

class PaymentProcessorTest extends TestCase {

  public function testNew() {
    $this->assertInstanceOf(
      \App\Domain\PaymentProccesor::class,
      new \App\Domain\PaymentProccesor()
    );
  }
}
