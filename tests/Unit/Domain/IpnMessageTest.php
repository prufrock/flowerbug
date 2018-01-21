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
}
