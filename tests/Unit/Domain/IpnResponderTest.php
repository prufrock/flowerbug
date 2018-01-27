<?php namespace Tests\Unit\Domain;

use App\Domain\IpnResponder;
use Tests\TestCase;
use Mockery as m;

class IpnResponderTest extends TestCase {

  public function testNew() {

    $responder = $this->app->make(\App\Domain\IpnResponder::class);

    $this->assertInstanceOf(
      \App\Domain\IpnResponder::class,
      $responder
    );
  }
  
  public function testGet() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($ipnDataStore, $verifierFactory);

    $this->assertEquals(1, $responder->get('txn_id', ['txn_id' => '1']));
  }

  public function testGetBuyersEmailAddress() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($ipnDataStore, $verifierFactory);

    $this->assertEquals(
      'buyer@example.com',
      $responder->getBuyersEmailAddress(['payer_email' => 'buyer@example.com'])
    );
  }

  public function testGetBuyersEmailAddressWithNoEmailAddress() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($ipnDataStore, $verifierFactory);

    $this->assertNotNull($responder->getBuyersEmailAddress(['payer_email' => '']));
    $this->assertEquals('', $responder->getBuyersEmailAddress(['payer_email' => '']));
  }

  public function testGetItemsPurchased() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($ipnDataStore, $verifierFactory);

    $this->assertEquals(
      ['technique201707', 'technique201708'],
      $responder->getItemsPurchased(
        [
          'item_number_1' => 'technique201707',
          'item_number_2' => 'technique201708'
        ]
      )
    );
  }
  
  public function testVerifyValidIpnMessage() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('storeMessage')->with(['txn_id' => 1])->andReturn(true);
    $ipnDataStore->shouldReceive('doesMessageExist')->with(['txn_id' => 1])->andReturn(false)->once();
    $verifier = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier->shouldReceive('compute')->andReturn(true);
    $responder = new IpnResponder($ipnDataStore, $verifier);
    
    $this->assertTrue($responder->verifyIpnMessage(['txn_id' => 1]));
  }

  public function testVerifyInvalidIpnMessage() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $verifier = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier->shouldReceive('compute')->andReturn(false);
    $responder = new IpnResponder($ipnDataStore, $verifier);

    $this->assertFalse($responder->verifyIpnMessage(['txn_id' => 1]));
  }

  public function testVerifyValidIpnMessageThatHasBeenReceivedBefore() {

    $verifier = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier->shouldReceive('compute')->andReturn(true);
    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('storeMessage')->with(['txn_id' => 1])->andReturn(true);
    $ipnDataStore->shouldReceive('doesMessageExist')->with(['txn_id' => 1])->andReturn(true)->once();
    $responder = new IpnResponder($ipnDataStore, $verifier);

    $this->assertFalse($responder->verifyIpnMessage(['txn_id' => 1]));
  }
}
