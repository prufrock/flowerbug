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
    $project = m::mock('\App\Domain\Project');
    $projects = collect([$project]);
    $project->shouldReceive('find')->andReturn($projects);
    $processor = new \App\Domain\PaymentProcessor($ipnResponder, $order, $project);

    $ipnResponder->shouldReceive('isVerified')
      ->once()
      ->with(['id' => '1'])
      ->andReturn(true);
    $ipnResponder->shouldReceive('hasBeenReceivedBefore')
      ->once()
      ->with(['id' => '1'])
      ->andReturn(false);
    $ipnResponder->shouldReceive('get')->withAnyArgs();
    $ipnResponder->shouldReceive('persist')
      ->once()
      ->with(['id' => '1']);
    $ipnResponder->shouldReceive('getItemsPurchased')
      ->with(['id' => '1'])
      ->andReturn(['technique201708']);
    $ipnResponder->shouldReceive('getBuyersEmailAddress')
      ->once()
      ->with(['id' => '1'])
      ->andReturn('d.kanen+flowerbugtest@gmail.com');

    $order->shouldReceive('fulfill')->with($projects, 'd.kanen+flowerbugtest@gmail.com')->once();

    $this->assertTrue($processor->process(['id' => '1']));
  }

  public function testUnableToVerifyMessage() {

    $ipnResponder = m::mock(\App\Domain\IpnResponder::class);
    $order = m::mock(\App\Domain\OrderFullFiller::class);
    $project = m::mock(\App\Domain\Project::class);
    $processor = new \App\Domain\PaymentProcessor($ipnResponder, $order, $project);

    $ipnResponder->shouldReceive('isVerified')
      ->once()
      ->with(['txn_id' => '1'])
      ->andReturn(false);
    $ipnResponder->shouldReceive('get')
      ->once()
      ->with('txn_id', ['txn_id' => '1']);
    $this->assertFalse($processor->process(['txn_id' => '1']));
  }

  public function testInvalidMessage() {

    $ipnResponder = m::mock(\App\Domain\IpnResponder::class);
    $order = m::mock(\App\Domain\OrderFullFiller::class);
    $project = m::mock(\App\Domain\Project::class);
    $processor = new \App\Domain\PaymentProcessor($ipnResponder, $order, $project);

    $ipnResponder->shouldReceive('isVerified')
      ->once()
      ->with(['txn_id' => '1'])
      ->andReturn(true);
    $ipnResponder->shouldReceive('hasBeenReceivedBefore')
      ->once()
      ->with(['txn_id' => '1'])
      ->andReturn(true);
    $ipnResponder->shouldReceive('get')
      ->once()
      ->with('txn_id', ['txn_id' => '1']);
    $this->assertFalse($processor->process(['txn_id' => '1']));
  }

  public function testHasntBeenReceivedBefore() {

    $ipnResponder = m::mock(\App\Domain\IpnResponder::class);
    $order = m::mock(\App\Domain\OrderFullFiller::class);
    $project = m::mock(\App\Domain\Project::class);
    $processor = new \App\Domain\PaymentProcessor(
      $ipnResponder,
      $order,
      $project
    );

    $ipnResponder->shouldReceive('isVerified')
      ->once()
      ->with(['txn_id' => '1'])
      ->andReturn(true);
    $ipnResponder->shouldReceive('hasBeenReceivedBefore')
      ->once()
      ->with(['txn_id' => '1'])
      ->andReturn(true);
    $ipnResponder->shouldReceive('get')
      ->once()
      ->with('txn_id', ['txn_id' => '1']);
    $this->assertFalse($processor->process(['txn_id' => '1']));
  }
}
