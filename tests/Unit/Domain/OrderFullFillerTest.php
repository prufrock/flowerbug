<?php namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class OrderFullFillerTest extends TestCase {

  public function testTransmit() {

    $transmitter = m::mock(\App\Domain\Transmitter::class);
    $transmitter->shouldReceive('transmit')->andReturn(true);
    $saleNotifier = m::mock(\App\Domain\SaleNotifier::class);
    $orderFullFiller = new \App\Domain\OrderFullFiller($transmitter, $saleNotifier);

    $this->assertTrue($orderFullFiller->transmit(''));
  }

  public function testNew() {

    $transmitter = m::mock(\App\Domain\Transmitter::class);
    $saleNotifier = m::mock(\App\Domain\SaleNotifier::class);

    $this->assertInstanceOf(
      \App\Domain\OrderFullFiller::class,
      new \App\Domain\OrderFullFiller($transmitter, $saleNotifier)
    );
  }

  public function testFulfillingAnOrder() {

    $transmitter = m::mock(\App\Domain\Transmitter::class);
    $saleNotifier = m::mock(\App\Domain\SaleNotifier::class);
    $orderFullFiller = new \App\Domain\OrderFullFiller($transmitter, $saleNotifier);

    $saleNotifier->shouldReceive('notify')->with($orderFullFiller)->andReturn(true)->once();

    $project = collect(new \App\Domain\Project);
    $buyersEmailAddress = 'buyer@example.com';

    $this->assertTrue($orderFullFiller->fulfill($project, $buyersEmailAddress));
    $this->assertEquals('buyer@example.com', $orderFullFiller->getBuyersEmailAddress());
    $this->assertEquals($project, $orderFullFiller->getProjects());
  }
}

