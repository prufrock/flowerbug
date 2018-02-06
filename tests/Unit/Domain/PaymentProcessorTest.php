<?php namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class PaymentProcessorTest extends TestCase {

  public function testPaymentProcessorRole() {

    $paymentProcessor = resolve(\App\Domain\PaymentProcessor::class);

    $this->assertTrue(method_exists($paymentProcessor, 'process'));
  }

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\PaymentProcessor::class,
      resolve(\App\Domain\PaymentProcessor::class)
    );
  }

  public function testValidMessage() {

    $ipnResponder = m::mock(\App\Domain\IpnResponder::class);
    $order = m::mock(\App\Domain\OrderFullFiller::class);
    $project = m::mock(\App\Domain\Project::class);
    $projects = collect([$project]);
    $project->shouldReceive('find')->andReturn($projects);
    $processor = new \App\Domain\PaymentProcessor($ipnResponder, $order, $project);

    $ipnMessage = m::mock(\App\Domain\IpnMessage::class);
    $ipnMessage->data = ['txn_id' => '1', 'payer_email' => 'd.kanen+flowerbugtest@gmail.com'];
    $ipnMessage->shouldReceive('verifyIpnMessage')->andReturn(true);
    $ipnMessage->shouldReceive('getBuyersEmailAddress')->andReturn('d.kanen+flowerbugtest@gmail.com');
    $ipnMessage->shouldReceive('getItemsPurchased')
      ->andReturn(['technique201708']);

    $order->shouldReceive('fulfill')->with($projects, 'd.kanen+flowerbugtest@gmail.com')->once();

    $this->assertTrue($processor->process(
      $ipnMessage
    ));
  }

  public function testUnableToVerifyMessage() {

    $ipnResponder = m::mock(\App\Domain\IpnResponder::class);
    $order = m::mock(\App\Domain\OrderFullFiller::class);
    $project = m::mock(\App\Domain\Project::class);
    $processor = new \App\Domain\PaymentProcessor($ipnResponder, $order, $project);
    
    $ipnMessage = m::mock(\App\Domain\IpnMessage::class);
    $ipnMessage->shouldReceive('verifyIpnMessage')->andReturn(false);

    $this->assertFalse($processor->process($ipnMessage));
  }

  public function testVerifiedMessageButNoItemsWerePurchased() {

    $ipnResponder = m::mock(\App\Domain\IpnResponder::class);
    $order = m::mock(\App\Domain\OrderFullFiller::class);
    $project = m::mock(\App\Domain\Project::class);
    $projects = collect([$project]);
    $project->shouldReceive('find')->andReturn($projects);
    $processor = new \App\Domain\PaymentProcessor($ipnResponder, $order, $project);

    $ipnMessage = m::mock(\App\Domain\IpnMessage::class);
    $ipnMessage->data = ['txn_id' => '1', 'payer_email' => 'd.kanen+flowerbugtest@gmail.com'];
    $ipnMessage->shouldReceive('verifyIpnMessage')->andReturn(true);
    $ipnMessage->shouldReceive('getBuyersEmailAddress')->andReturn('d.kanen+flowerbugtest@gmail.com');
    $ipnMessage->shouldReceive('get')->atLeast()->times(1)->andReturn(1);
    $ipnMessage->shouldReceive('getItemsPurchased')
      ->andReturn([]);


    $this->assertTrue($processor->process(
      $ipnMessage
    ));
  }
}
