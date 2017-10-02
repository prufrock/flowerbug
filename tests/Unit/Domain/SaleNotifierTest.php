<?php namespace Tests\Unit\Domain;

use Tests\TestCase;

class SaleNotifierTest extends TestCase {

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\SaleNotifier::class,
      new \App\Domain\SaleNotifier()
    );
  }

  public function testSuccessfulNotify() {

    $this->assertTrue((new \App\Domain\SaleNotifier)->notify());
  }
}
