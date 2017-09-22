<?php namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class OrderFullFillerTest extends TestCase {

  public function testNew() {

    $transmitter = m::mock('\App\Domain\TransmitterSES');
    $saleNotifier = m::mock('\App\Domain\SaleNotifier');

    $this->assertInstanceOf(
      \App\Domain\OrderFullFiller::class,
      new \App\Domain\OrderFullFiller($transmitter, $saleNotifier)
    );
  }

  public function testFulfillingAnOrder() {

    $transmitter = m::mock('\App\Domain\TransmitterSES');
    $saleNotifier = m::mock('\App\Domain\SaleNotifier');
    $orderFullFiller = new \App\Domain\OrderFullFiller($transmitter, $saleNotifier);

    $transmitter->shouldReceive('setOrder')->with($orderFullFiller)->once();

    $saleNotifier->shouldReceive('initialize')->with($orderFullFiller)->once();
    $saleNotifier->shouldReceive('notify')->andReturn(true)->once();

    $itemsPurchased = ['technique201709'];
    $buyersEmailAddress = 'buyer@example.com';

    $this->assertTrue($orderFullFiller->fulfill($itemsPurchased, $buyersEmailAddress));
    $this->assertEquals('buyer@example.com', $orderFullFiller->getBuyersEmailAddress());
    $this->assertEquals($itemsPurchased, $orderFullFiller->getItemsPurchased());
    $this->assertEquals($transmitter, $orderFullFiller->getTransmitter());
  }
}