<?php namespace Tests\Unit\Domain;

use App\Domain\IpnMessage;
use Tests\TestCase;

class IpnMessageTest extends TestCase {

  public function testNew() {

    $this->assertInstanceOf(
      IpnMessage::class,
      new IpnMessage()
    );
  }

  public function testGetASingleKey() {

    $ipnMessage = new IpnMessage();
    $ipnMessage->data = ['txn_id' => 1];
    
    $this->assertEquals($ipnMessage->data['txn_id'], 1);
  }

  public function testGetTheWholeMessageAsAnArray() {

    $ipnMessage = new IpnMessage();
    $ipnMessage->data = ['txn_id' => 1];

    $this->assertEquals($ipnMessage->data, ['txn_id' => 1]);
  }
  
  public function testGetBuyersEmailAddress() {

    $ipnMessage = new IpnMessage();
    $ipnMessage->data = ['payer_email' => 'buyer@example.com'];

    $this->assertEquals($ipnMessage->getBuyersEmailAddress(), 'buyer@example.com');
  }

  public function testGetEmptyBuyersEmailAddress() {

    $ipnMessage = new IpnMessage();
    $ipnMessage->data = [];

    $this->assertEquals($ipnMessage->getBuyersEmailAddress(), '');
  }
}
